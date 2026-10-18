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

        if (! User::query()->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $documentStatusList = CodeList::query()->firstOrCreate(
            ['key' => 'role_codelist'],
            ['name' => 'Roles for application'],
        );

        foreach ([
            ['code' => 'admin', 'label' => 'Admin', 'sort_order' => 10, 'active' => true],
            ['code' => 'editor', 'label' => 'Editor', 'sort_order' => 20, 'active' => true],
            ['code' => 'viewer', 'label' => 'Viewer', 'sort_order' => 30, 'active' => true],
        ] as $item) {
            $documentStatusList->items()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'label' => $item['label'],
                    'sort_order' => $item['sort_order'],
                    'active' => $item['active'],
                ],
            );
        }
    }
}
