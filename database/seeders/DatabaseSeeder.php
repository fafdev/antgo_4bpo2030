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

        $adminRole = $documentStatusList->items()->where('code', 'admin')->firstOrFail();

        $documentStatusList = CodeList::query()->firstOrCreate(
            ['key' => 'document_status_codelist'],
            ['name' => 'Document Statuses'],
        );

        foreach ([
            ['code' => 'draft', 'label' => 'Draft', 'sort_order' => 10, 'active' => true],
            ['code' => 'submitted', 'label' => 'Submitted', 'sort_order' => 20, 'active' => true],
            ['code' => 'approved', 'label' => 'Approved', 'sort_order' => 30, 'active' => true],
            ['code' => 'rejected', 'label' => 'Rejected', 'sort_order' => 40, 'active' => true],
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

        $adminRole = $documentStatusList->items()->where('code', 'draft')->firstOrFail();

        $documentStatusList = CodeList::query()->firstOrCreate(
            ['key' => 'days_codelist'],
            ['name' => 'Dias de la semana'],
        );

        foreach ([
            ['code' => '1', 'label' => 'Lunes', 'sort_order' => 10, 'active' => true],
            ['code' => '2', 'label' => 'Martes', 'sort_order' => 20, 'active' => true],
            ['code' => '3', 'label' => 'Miércoles', 'sort_order' => 30, 'active' => true],
            ['code' => '4', 'label' => 'Jueves', 'sort_order' => 40, 'active' => true],
            ['code' => '5', 'label' => 'Viernes', 'sort_order' => 50, 'active' => true],
            ['code' => '6', 'label' => 'Sábado', 'sort_order' => 60, 'active' => true],
            ['code' => '7', 'label' => 'Domingo', 'sort_order' => 70, 'active' => true],
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

        $documentStatusList = CodeList::query()->firstOrCreate(
            ['key' => 'jobs_codelist'],
            ['name' => 'Descripción de empleos'],
        );

        foreach ([
            ['code' => '1', 'label' => 'Profesor/a', 'sort_order' => 10, 'active' => true],
            ['code' => '2', 'label' => 'Ingeniero/a', 'sort_order' => 20, 'active' => true],
            ['code' => '3', 'label' => 'Arquitecto/a', 'sort_order' => 30, 'active' => true],
            ['code' => '4', 'label' => 'Abogado/a', 'sort_order' => 40, 'active' => true],
            ['code' => '5', 'label' => 'Financiero/a', 'sort_order' => 50, 'active' => true],
            ['code' => '6', 'label' => 'Informático/a', 'sort_order' => 60, 'active' => true],
            ['code' => '7', 'label' => 'Administrativo/a', 'sort_order' => 70, 'active' => true],
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

        if (app()->environment(['local', 'testing'])) {
            User::query()
                ->where('email', 'test@example.com')
                ->update(['role_codelist' => $adminRole->id]);
        }
    }
}
