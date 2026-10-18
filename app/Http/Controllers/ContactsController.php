<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contacts\ContactImportCsvRequest;
use App\Http\Requests\StoreContactsRequest;
use App\Http\Requests\UpdateContactsRequest;
use App\Models\Contacts;
use App\Support\SpanishTaxId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use SplFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Contacts::class);

        $contacts = Contacts::query()
            ->orderBy('code')
            ->get()
            ->map(fn (Contacts $contact): array => [
                'id' => $contact->id,
                'code' => $contact->code,
                'type' => $contact->type,
                'taxId' => $contact->taxId,
                'formatedName' => $contact->formatedName,
                'name' => $contact->name,
                'middleName' => $contact->middleName,
                'lastName' => $contact->lastName,
                'companyName' => $contact->companyName,
                'gender' => $contact->gender,
                'birthDate' => $contact->birthDate?->toDateString(),
                'email' => $contact->email,
                'phone' => $contact->phone,
                'mobilePhone' => $contact->mobilePhone,
                'created_at' => $contact->created_at?->toDateTimeString(),
                'updated_at' => $contact->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('contacts/index', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactsRequest $request): RedirectResponse
    {
        $validatedPayload = $request->validated();

        DB::transaction(function () use ($validatedPayload): void {
            $contact = Contacts::query()->create(
                $this->normalizePayload($validatedPayload),
            );

            if (array_key_exists('addresses', $validatedPayload)) {
                $this->syncAddresses($contact, $validatedPayload['addresses']);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contacto creado.')]);

        return to_route('contacts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contacts $contact): Response
    {
        $this->authorize('view', $contact);

        return Inertia::render('contacts/edit', [
            'contact' => [
                'id' => $contact->id,
                'code' => $contact->code,
                'type' => $contact->type,
                'taxId' => $contact->taxId,
                'formatedName' => $contact->formatedName,
                'name' => $contact->name,
                'middleName' => $contact->middleName,
                'lastName' => $contact->lastName,
                'companyName' => $contact->companyName,
                'gender' => $contact->gender,
                'birthDate' => $contact->birthDate?->toDateString(),
                'email' => $contact->email,
                'phone' => $contact->phone,
                'mobilePhone' => $contact->mobilePhone,
                'addresses' => $contact->addresses()
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($address): array => [
                        'id' => $address->id,
                        'street' => $address->street,
                        'number' => $address->number,
                        'building' => $address->building,
                        'floor' => $address->floor,
                        'door' => $address->door,
                        'city' => $address->city,
                        'postal_code' => $address->postal_code,
                        'country' => $address->country,
                        'is_primary' => $address->is_primary,
                    ])
                    ->values()
                    ->all(),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactsRequest $request, Contacts $contact): RedirectResponse
    {
        $validatedPayload = $request->validated();

        DB::transaction(function () use ($contact, $validatedPayload): void {
            $contact->update($this->normalizePayload($validatedPayload, $contact->id, $contact->type, $contact->code));

            if (array_key_exists('addresses', $validatedPayload)) {
                $this->syncAddresses($contact, $validatedPayload['addresses']);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contacto actualizado.')]);

        return to_route('contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contacts $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        DB::transaction(function () use ($contact): void {
            $contact->addresses()->delete();
            $contact->delete();
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contacto eliminado.')]);

        return to_route('contacts.index');
    }

    public function downloadTemplateCsv(): StreamedResponse
    {
        $this->authorize('viewAny', Contacts::class);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        return response()->streamDownload(static function (): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                throw new RuntimeException('Unable to generate CSV template.');
            }

            fputcsv($handle, ['taxId', 'name', 'middleName', 'lastName', 'companyName', 'gender', 'birthDate', 'email', 'phone']);
            fputcsv($handle, ['12345678Z', 'Ana', 'María', 'López', '', 'Mujer', '1990-01-15', 'ana.lopez@example.com', '+34111111111']);
            fputcsv($handle, ['A58818501', '', '', '', 'Empresa Ejemplo S.L.', '', '', 'info@empresa-ejemplo.com', '+34999999999']);
            fclose($handle);
        }, 'contacts-template.csv', $headers);
    }

    /**
     * Import contacts from a CSV file.
     */
    public function importCsv(ContactImportCsvRequest $request): RedirectResponse
    {
        $file = new SplFileObject($request->file('csv_file')->getPathname());
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $headers = null;
        $created = 0;
        $skipped = 0;
        $lineNumber = 0;

        DB::transaction(function () use ($file, &$headers, &$created, &$skipped, &$lineNumber): void {
            foreach ($file as $row) {
                $lineNumber++;

                if ($row === false || $row === [null]) {
                    continue;
                }

                $row = array_map(static fn ($value): string => trim((string) $value), $row);

                if ($headers === null) {
                    $headers = array_map(static fn (string $value): string => mb_strtolower($value), $row);
                    $requiredHeaders = ['taxid'];

                    foreach ($requiredHeaders as $header) {
                        if (! in_array($header, $headers, true)) {
                            throw ValidationException::withMessages([
                                'csv_file' => __("CSV header is missing ':header' column.", ['header' => $header]),
                            ]);
                        }
                    }

                    continue;
                }

                $payload = $this->mapRowToPayload($headers, $row);
                $validator = Validator::make($payload, [
                    'taxId' => [
                        'required',
                        'string',
                        'max:20',
                        static function (string $attribute, mixed $value, \Closure $fail): void {
                            if (! SpanishTaxId::isValid((string) $value)) {
                                $fail(__('The :attribute must be a valid Spanish CIF, NIF or NIE.', ['attribute' => $attribute]));
                            }
                        },
                    ],
                    'name' => ['nullable', 'string', 'max:255'],
                    'middleName' => ['nullable', 'string', 'max:255'],
                    'lastName' => ['nullable', 'string', 'max:255'],
                    'companyName' => ['nullable', 'string', 'max:255'],
                    'gender' => ['nullable', 'in:Hombre,Mujer,Other'],
                    'birthDate' => ['nullable', 'date'],
                    'email' => ['nullable', 'email', 'max:255'],
                    'phone' => ['nullable', 'string', 'max:50'],
                    'mobilePhone' => ['nullable', 'string', 'max:50'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'csv_file' => __('Invalid data at CSV row :row: :message', [
                            'row' => $lineNumber,
                            'message' => $validator->errors()->first(),
                        ]),
                    ]);
                }

                if (Contacts::query()->where('taxId', $payload['taxId'])->exists()) {
                    $skipped++;

                    continue;
                }

                Contacts::query()->create($this->normalizePayload($payload));
                $created++;
            }

            if ($headers === null) {
                throw ValidationException::withMessages([
                    'csv_file' => __('The CSV file is empty.'),
                ]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Import completed. Created: :created. Skipped: :skipped.', [
                'created' => $created,
                'skipped' => $skipped,
            ]),
        ]);

        return to_route('contacts.index');
    }

    /**
     * @param  array{taxId: string, name?: ?string, middleName?: ?string, lastName?: ?string, companyName?: ?string, gender?: ?string, birthDate?: ?string, email?: ?string, phone?: ?string}  $payload
     * @param  'Persona'|'Empresa'|null  $lockedType
     * @return array{id: string, code: string, type: 'Persona'|'Empresa', taxId: string, formatedName: string, name: ?string, middleName: ?string, lastName: ?string, companyName: ?string, gender: ?string, birthDate: ?string, email: ?string, phone: ?string, mobilePhone: ?string}
     */
    private function normalizePayload(array $payload, ?string $id = null, ?string $lockedType = null, ?string $lockedCode = null): array
    {
        $taxId = SpanishTaxId::normalize($payload['taxId'] ?? null);
        $type = SpanishTaxId::detectType($taxId);

        if ($taxId === null || $type === null) {
            throw ValidationException::withMessages([
                'taxId' => __('El taxId debe ser un CIF, NIF o NIE español válido.'),
            ]);
        }

        if ($lockedType !== null && $type !== $lockedType) {
            throw ValidationException::withMessages([
                'taxId' => __('El taxId debe corresponder al tipo de contacto existente (:type).', ['type' => $lockedType]),
            ]);
        }

        $name = $this->toNullableString($payload['name'] ?? null);
        $middleName = $this->toNullableString($payload['middleName'] ?? null);
        $lastName = $this->toNullableString($payload['lastName'] ?? null);
        $companyName = $this->toNullableString($payload['companyName'] ?? null);
        $gender = $this->toNullableString($payload['gender'] ?? null);
        $birthDate = $this->toNullableString($payload['birthDate'] ?? null);
        $email = $this->toNullableString($payload['email'] ?? null);
        $phone = $this->toNullableString($payload['phone'] ?? null);
        $mobilePhone = $this->toNullableString($payload['mobilePhone'] ?? null);

        if ($type === 'Empresa') {
            if ($companyName === null) {
                throw ValidationException::withMessages([
                    'companyName' => __('El campo companyName es obligatorio cuando el taxId es CIF.'),
                ]);
            }

            $name = null;
            $middleName = null;
            $lastName = null;
            $gender = null;
            $birthDate = null;
            $formatedName = $companyName;
        } else {
            if ($name === null || $lastName === null) {
                throw ValidationException::withMessages([
                    'name' => __('El campo name y lastName son obligatorios cuando el taxId es NIF o NIE.'),
                ]);
            }

            $companyName = null;
            $formatedName = trim(implode(' ', array_filter([$name, $middleName, $lastName], static fn (?string $value): bool => $value !== null && $value !== '')));
        }

        return [
            'id' => $id ?? (string) Str::uuid(),
            'code' => $lockedCode ?? $this->generateNextCode(),
            'type' => $type,
            'taxId' => $taxId,
            'formatedName' => $formatedName,
            'name' => $name,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'companyName' => $companyName,
            'gender' => $gender,
            'birthDate' => $birthDate,
            'email' => $email,
            'phone' => $phone,
            'mobilePhone' => $mobilePhone,
        ];
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string>  $row
     * @return array{taxId: string, name: ?string, middleName: ?string, lastName: ?string, companyName: ?string, gender: ?string, birthDate: ?string, email: ?string, phone: ?string, mobilePhone: ?string}
     */
    private function mapRowToPayload(array $headers, array $row): array
    {
        $values = array_pad($row, count($headers), null);
        $values = array_slice($values, 0, count($headers));
        $mappedRow = array_combine($headers, $values);

        if ($mappedRow === false) {
            throw ValidationException::withMessages([
                'csv_file' => __('Unable to parse CSV row.'),
            ]);
        }

        $taxId = $mappedRow['taxid'] ?? $mappedRow['tacid'] ?? null;

        return [
            'taxId' => SpanishTaxId::normalize(is_string($taxId) ? $taxId : null) ?? '',
            'name' => $this->toNullableString($mappedRow['name'] ?? null),
            'middleName' => $this->toNullableString($mappedRow['middlename'] ?? null),
            'lastName' => $this->toNullableString($mappedRow['lastname'] ?? null),
            'companyName' => $this->toNullableString($mappedRow['companyname'] ?? null),
            'gender' => $this->toNullableString($mappedRow['gender'] ?? null),
            'birthDate' => $this->toNullableString($mappedRow['birthdate'] ?? null),
            'email' => $this->toNullableString($mappedRow['email'] ?? null),
            'phone' => $this->toNullableString($mappedRow['phone'] ?? null),
            'mobilePhone' => $this->toNullableString($mappedRow['mobilephone'] ?? null),
        ];
    }

    private function toNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    /**
     * @param  array<int, array<string, mixed>>  $addresses
     */
    private function syncAddresses(Contacts $contact, array $addresses): void
    {
        $normalizedAddresses = collect($addresses)
            ->map(function (array $address): array {
                $rawId = $address['id'] ?? null;

                return [
                    'id' => is_numeric($rawId) ? (int) $rawId : null,
                    'street' => trim((string) ($address['street'] ?? '')),
                    'number' => $this->toNullableString($address['number'] ?? null),
                    'building' => $this->toNullableString($address['building'] ?? null),
                    'floor' => $this->toNullableString($address['floor'] ?? null),
                    'door' => $this->toNullableString($address['door'] ?? null),
                    'city' => trim((string) ($address['city'] ?? '')),
                    'postal_code' => $this->toNullableString($address['postal_code'] ?? null),
                    'country' => $this->toNullableString($address['country'] ?? null),
                    'is_primary' => (bool) ($address['is_primary'] ?? false),
                ];
            })
            ->filter(static fn (array $address): bool => $address['street'] !== '' && $address['city'] !== '')
            ->values();

        $primaryCount = $normalizedAddresses->where('is_primary', true)->count();

        if ($primaryCount > 1) {
            throw ValidationException::withMessages([
                'addresses' => __('Solo se permite una dirección principal por contacto.'),
            ]);
        }

        if ($normalizedAddresses->isNotEmpty() && $primaryCount === 0) {
            $normalizedAddresses[0]['is_primary'] = true;
        }

        $existingAddresses = $contact->addresses()->get()->keyBy('id');
        $keptAddressIds = [];

        foreach ($normalizedAddresses as $address) {
            $addressId = $address['id'];
            unset($address['id']);

            if ($addressId !== null) {
                if (! $existingAddresses->has($addressId)) {
                    throw ValidationException::withMessages([
                        'addresses' => __('Identificador de dirección no válido proporcionado para este contacto.'),
                    ]);
                }

                $existingAddresses->get($addressId)?->update($address);
                $keptAddressIds[] = $addressId;

                continue;
            }

            $createdAddress = $contact->addresses()->create($address);
            $keptAddressIds[] = $createdAddress->id;
        }

        if ($keptAddressIds === []) {
            $contact->addresses()->delete();

            return;
        }

        $contact->addresses()->whereNotIn('id', $keptAddressIds)->delete();
    }

    private function generateNextCode(): string
    {
        $lastCode = Contacts::query()
            ->where('code', 'like', 'CT-%')
            ->orderByDesc('code')
            ->value('code');

        $nextNumber = 1;

        if (is_string($lastCode) && preg_match('/^CT-(\d{7})$/', $lastCode, $matches) === 1) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        while (true) {
            $code = sprintf('CT-%07d', $nextNumber);

            if (! Contacts::query()->where('code', $code)->exists()) {
                return $code;
            }

            $nextNumber++;
        }
    }
}
