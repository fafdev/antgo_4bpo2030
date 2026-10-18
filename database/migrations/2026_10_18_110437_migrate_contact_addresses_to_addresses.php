<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table): void {
            $table->boolean('is_primary')->default(false);
        });

        DB::table('contact_addresses')->orderBy('id')->chunkById(500, function (Collection $addresses): void {
            foreach ($addresses as $address) {
                DB::table('addresses')->insert([
                    'street' => $address->street,
                    'number' => $address->number,
                    'building' => $address->building,
                    'floor' => $address->floor,
                    'door' => $address->door,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'is_primary' => $address->is_primary,
                    'addressable_type' => 'App\\Models\\Contacts',
                    'addressable_id' => $address->contact_id,
                    'created_at' => $address->created_at,
                    'updated_at' => $address->updated_at,
                ]);
            }
        });

        Schema::drop('contact_addresses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('contact_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('street');
            $table->string('number', 50)->nullable();
            $table->string('building', 100)->nullable();
            $table->string('floor', 50)->nullable();
            $table->string('door', 50)->nullable();
            $table->string('city');
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        DB::table('addresses')
            ->where('addressable_type', 'App\\Models\\Contacts')
            ->orderBy('id')
            ->chunkById(500, function (Collection $addresses): void {
                foreach ($addresses as $address) {
                    DB::table('contact_addresses')->insert([
                        'id' => $address->id,
                        'contact_id' => $address->addressable_id,
                        'street' => $address->street,
                        'number' => $address->number,
                        'building' => $address->building,
                        'floor' => $address->floor,
                        'door' => $address->door,
                        'city' => $address->city,
                        'postal_code' => $address->postal_code,
                        'country' => $address->country,
                        'is_primary' => $address->is_primary,
                        'created_at' => $address->created_at,
                        'updated_at' => $address->updated_at,
                    ]);
                }
            });

        DB::table('addresses')
            ->where('addressable_type', 'App\\Models\\Contacts')
            ->delete();

        Schema::table('addresses', function (Blueprint $table): void {
            $table->dropColumn('is_primary');
        });
    }
};
