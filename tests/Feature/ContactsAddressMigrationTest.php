<?php

use App\Models\Contacts;
use Illuminate\Support\Facades\DB;

test('existing contact addresses migrate to the polymorphic addresses table', function () {
    $migration = require database_path('migrations/2026_10_18_110437_migrate_contact_addresses_to_addresses.php');
    $migration->down();

    $contact = Contacts::factory()->create();

    DB::table('contact_addresses')->insert([
        'contact_id' => $contact->id,
        'street' => 'Carrer Major',
        'number' => '10',
        'building' => null,
        'floor' => null,
        'door' => null,
        'city' => 'Barcelona',
        'postal_code' => '08001',
        'country' => 'Espanya',
        'is_primary' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration->up();

    $this->assertDatabaseHas('addresses', [
        'addressable_type' => Contacts::class,
        'addressable_id' => $contact->id,
        'street' => 'Carrer Major',
        'number' => '10',
        'city' => 'Barcelona',
        'postal_code' => '08001',
        'country' => 'Espanya',
        'is_primary' => true,
    ]);
});
