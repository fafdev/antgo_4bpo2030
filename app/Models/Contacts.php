<?php

namespace App\Models;

use Database\Factories\ContactsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $code
 * @property string $type
 * @property string $taxId
 * @property string $formatedName
 * @property string|null $name
 * @property string|null $middleName
 * @property string|null $lastName
 * @property string|null $companyName
 * @property string|null $gender
 * @property Carbon|null $birthDate
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobilePhone
 * @property int|null $jobs_codelist
 */
#[Fillable(['id', 'code', 'type', 'taxId', 'formatedName', 'jobs_codelist', 'name', 'middleName', 'lastName', 'companyName', 'gender', 'birthDate', 'email', 'phone', 'mobilePhone'])]
class Contacts extends Model
{
    /** @use HasFactory<ContactsFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return BelongsTo<CodeListItem, $this>
     */
    public function jobsCodelist(): BelongsTo
    {
        return $this->belongsTo(CodeListItem::class, 'jobs_codelist');
    }

    /**
     * @return MorphMany<Address, $this>
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthDate' => 'date',
        ];
    }
}
