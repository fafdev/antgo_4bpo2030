<?php

use App\Models\CodeList;
use App\Models\CodeListItem;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

function createAdminUser(): User
{
    $roleList = CodeList::factory()->create(['key' => 'role_codelist']);
    $adminRole = CodeListItem::factory()->for($roleList)->create([
        'code' => 'admin',
        'label' => 'Admin',
        'sort_order' => 10,
        'active' => true,
    ]);

    return User::factory()->create([
        'name' => 'Administrator',
        'role_codelist' => $adminRole->id,
    ]);
}

test('administrators can view users and active application roles', function () {
    $admin = createAdminUser();
    $role = CodeListItem::factory()->for($admin->roleCodelist->codeList)->create([
        'code' => 'editor',
        'label' => 'Editor',
        'sort_order' => 20,
        'active' => true,
    ]);
    $managedUser = User::factory()->create([
        'name' => 'Zed User',
        'role_codelist' => $role->id,
    ]);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->where('auth.is_admin', true)
            ->has('users', 2)
            ->where('users.1.role_label', 'Editor')
            ->has('roles', 2)
            ->where('roles.1.label', 'Editor'),
        );
});

test('administrators can create users and email a password setup link', function () {
    $admin = createAdminUser();
    $role = CodeListItem::factory()->for($admin->roleCodelist->codeList)->create([
        'code' => 'editor',
        'label' => 'Editor',
        'sort_order' => 20,
        'active' => true,
    ]);
    Notification::fake();

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'new-user@example.com',
        'role_codelist' => $role->id,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('users.index'));

    $user = User::query()->where('email', 'new-user@example.com')->firstOrFail();
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New User',
        'role_codelist' => $role->id,
        'email_verified_at' => null,
    ]);
    Notification::assertSentTo($user, ResetPassword::class);
});

test('administrators can edit another user profile and role', function () {
    $admin = createAdminUser();
    $role = CodeListItem::factory()->for($admin->roleCodelist->codeList)->create([
        'code' => 'editor',
        'label' => 'Editor',
        'sort_order' => 20,
        'active' => true,
    ]);
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.edit', $user))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/edit')
            ->where('user.id', $user->id)
            ->where('isSelf', false)
            ->has('roles', 2),
        );

    $response = $this->put(route('users.update', $user), [
        'name' => 'Updated User',
        'email' => 'updated-user@example.com',
        'role_codelist' => $role->id,
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated User',
        'email' => 'updated-user@example.com',
        'role_codelist' => $role->id,
        'email_verified_at' => null,
    ]);
});

test('administrators cannot assign roles outside the role code list', function () {
    $admin = createAdminUser();
    $otherList = CodeList::factory()->create();
    $otherRole = CodeListItem::factory()->for($otherList)->create();

    $response = $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Invalid User',
            'email' => 'invalid-user@example.com',
            'role_codelist' => $otherRole->id,
        ]);

    $response->assertSessionHasErrors('role_codelist');
    $this->assertDatabaseMissing('users', ['email' => 'invalid-user@example.com']);
});

test('administrators cannot change their own role', function () {
    $admin = createAdminUser();
    $editorRole = CodeListItem::factory()->for($admin->roleCodelist->codeList)->create([
        'code' => 'editor',
        'label' => 'Editor',
        'sort_order' => 20,
        'active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_codelist' => $editorRole->id,
        ]);

    $response->assertSessionHasErrors('role_codelist');
    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
        'role_codelist' => $admin->role_codelist,
    ]);
});

test('non-administrators cannot access user management', function () {
    $user = User::factory()->create();
    $managedUser = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.is_admin', false),
        );

    $this->actingAs($user)
        ->get(route('users.edit', $managedUser))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('users.store'), [
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('users.update', $managedUser), [
            'name' => $managedUser->name,
            'email' => $managedUser->email,
        ])
        ->assertForbidden();
});

test('guests cannot access user management', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});
