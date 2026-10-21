<?php

use App\Models\CodeListItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not duplicate seeded records when run more than once', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('code_lists', 1);
    $this->assertDatabaseCount('code_list_items', 3);
    expect(User::query()->firstOrFail()->role_codelist)
        ->toBe(CodeListItem::query()->where('code', 'admin')->value('id'));
});
