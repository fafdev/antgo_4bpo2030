<?php

namespace Database\Seeders;

use App\Models\CodeList;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $documentStatusList = CodeList::query()->create([
            'key' => 'document_status',
            'name' => 'Document Status',
        ]);

        $documentStatusList->items()->createMany([
            ['code' => 'draft', 'label' => 'Draft', 'sort_order' => 10, 'active' => true],
            ['code' => 'published', 'label' => 'Published', 'sort_order' => 20, 'active' => true],
            ['code' => 'archived', 'label' => 'Archived', 'sort_order' => 30, 'active' => true],
        ]);
    }
}
