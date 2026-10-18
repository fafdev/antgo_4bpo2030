<?php

use App\Models\Contacts;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;

test('authenticated users can create a contact with multiple addresses', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('contacts.store'), [
        'taxId' => '12345678Z',
        'name' => 'Ana',
        'middleName' => 'Maria',
        'lastName' => 'Lopez',
        'gender' => 'Mujer',
        'addresses' => [
            [
                'street' => 'Carrer Major',
                'number' => '10',
                'city' => 'Barcelona',
                'country' => 'Espanya',
                'is_primary' => true,
            ],
            [
                'street' => 'Carrer Nou',
                'number' => '20',
                'city' => 'Girona',
                'country' => 'Espanya',
                'is_primary' => false,
            ],
        ],
    ]);

    $contact = Contacts::query()->where('taxId', '12345678Z')->firstOrFail();

    $response->assertRedirect(route('contacts.index'));
    $this->assertModelExists($contact);
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'code' => 'CT-0000001',
        'type' => 'Persona',
        'formatedName' => 'Ana Maria Lopez',
    ]);
    $this->assertDatabaseHas('addresses', [
        'addressable_type' => Contacts::class,
        'addressable_id' => $contact->id,
        'street' => 'Carrer Major',
        'city' => 'Barcelona',
        'is_primary' => true,
    ]);
    $this->assertDatabaseHas('addresses', [
        'addressable_type' => Contacts::class,
        'addressable_id' => $contact->id,
        'street' => 'Carrer Nou',
        'city' => 'Girona',
        'is_primary' => false,
    ]);
    $this->assertDatabaseCount('addresses', 2);
});

test('authenticated users can edit contacts and their addresses', function () {
    $user = User::factory()->create();
    $contact = Contacts::factory()->create();
    $address = $contact->addresses()->create([
        'street' => 'Old Street',
        'city' => 'Old City',
        'is_primary' => true,
    ]);
    $addressToRemove = $contact->addresses()->create([
        'street' => 'Second Street',
        'city' => 'Second City',
    ]);

    $editResponse = $this
        ->actingAs($user)
        ->get(route('contacts.edit', $contact));

    $editResponse
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('contacts/edit')
            ->where('contact.addresses.0.id', $address->id)
            ->where('contact.addresses.1.id', $addressToRemove->id),
        );

    $response = $this->put(route('contacts.update', $contact), [
        'taxId' => $contact->taxId,
        'name' => 'Updated',
        'lastName' => 'Contact',
        'email' => 'updated@example.com',
        'addresses' => [
            [
                'id' => $address->id,
                'street' => 'New Street',
                'city' => 'New City',
                'is_primary' => true,
            ],
            [
                'street' => 'New Secondary Street',
                'city' => 'New Secondary City',
                'is_primary' => false,
            ],
        ],
    ]);

    $response->assertRedirect(route('contacts.index'));
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'formatedName' => 'Updated Contact',
        'email' => 'updated@example.com',
    ]);
    $this->assertDatabaseHas('addresses', [
        'id' => $address->id,
        'addressable_type' => Contacts::class,
        'addressable_id' => $contact->id,
        'street' => 'New Street',
        'city' => 'New City',
    ]);
    $this->assertModelMissing($addressToRemove);
    $this->assertDatabaseHas('addresses', [
        'addressable_type' => Contacts::class,
        'addressable_id' => $contact->id,
        'street' => 'New Secondary Street',
        'city' => 'New Secondary City',
        'is_primary' => false,
    ]);
    $this->assertDatabaseCount('addresses', 2);
});

test('contacts cannot update an address owned by another contact', function () {
    $user = User::factory()->create();
    $contact = Contacts::factory()->create();
    $otherContact = Contacts::factory()->create();
    $otherAddress = $otherContact->addresses()->create([
        'street' => 'Private Street',
        'city' => 'Private City',
        'is_primary' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('contacts.update', $contact), [
            'taxId' => $contact->taxId,
            'addresses' => [
                [
                    'id' => $otherAddress->id,
                    'street' => 'Modified Street',
                    'city' => 'Modified City',
                ],
            ],
        ]);

    $response->assertSessionHasErrors('addresses.0.id');
    $this->assertDatabaseHas('addresses', [
        'id' => $otherAddress->id,
        'addressable_type' => Contacts::class,
        'addressable_id' => $otherContact->id,
        'street' => 'Private Street',
        'city' => 'Private City',
    ]);
    $this->assertDatabaseCount('addresses', 1);
});

test('authenticated users can import valid contacts from CSV', function () {
    $user = User::factory()->create();
    $csv = UploadedFile::fake()->createWithContent(
        'contacts.csv',
        "taxId,name,lastName,gender\n12345678Z,Ana,Lopez,Mujer\n",
    );

    $response = $this->actingAs($user)->post(route('contacts.import-csv'), [
        'csv_file' => $csv,
    ]);

    $response->assertRedirect(route('contacts.index'));
    $this->assertDatabaseHas('contacts', [
        'taxId' => '12345678Z',
        'formatedName' => 'Ana Lopez',
        'gender' => 'Mujer',
    ]);
    $this->assertDatabaseCount('contacts', 1);
});

test('deleting a contact removes its associated addresses', function () {
    $user = User::factory()->create();
    $contact = Contacts::factory()->create();
    $address = $contact->addresses()->create([
        'street' => 'Carrer Major',
        'city' => 'Barcelona',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('contacts.destroy', $contact));

    $response->assertRedirect(route('contacts.index'));
    $this->assertModelMissing($contact);
    $this->assertModelMissing($address);
});

test('guest cannot create contacts', function () {
    $response = $this->post(route('contacts.store'), [
        'taxId' => '12345678Z',
        'name' => 'Ana',
        'lastName' => 'Lopez',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('contacts', 0);
});
