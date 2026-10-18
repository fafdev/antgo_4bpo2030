<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('authenticated user can open contacts from the application menu', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('contacts.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('contacts/index')
            ->has('contacts', 0),
        );
});

test('guest cannot open the contacts page', function () {
    $response = $this->get(route('contacts.index'));

    $response->assertRedirect(route('login'));
});
