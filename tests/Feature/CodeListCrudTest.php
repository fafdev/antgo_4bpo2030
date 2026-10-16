<?php

use App\Models\CodeList;
use App\Models\CodeListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('authenticated user can view code lists page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('code-lists.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('code-lists/index')
            ->where('filters.search', ''),
        );
});

test('user can filter code lists by description', function () {
    $user = User::factory()->create();
    CodeList::factory()->create(['name' => 'Document Status']);
    CodeList::factory()->create(['name' => 'Payment Method']);

    $this->actingAs($user)
        ->get(route('code-lists.index', ['search' => 'Document']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('code-lists/index')
            ->has('codeLists', 1)
            ->where('codeLists.0.name', 'Document Status')
            ->where('filters.search', 'Document'),
        );
});

test('code list description search returns no lists when nothing matches', function () {
    $user = User::factory()->create();
    CodeList::factory()->create(['name' => 'Payment Method']);

    $this->actingAs($user)
        ->get(route('code-lists.index', ['search' => 'Document']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('code-lists/index')
            ->has('codeLists', 0)
            ->where('filters.search', 'Document'),
        );
});

test('guest cannot view code lists page', function () {
    $response = $this->get(route('code-lists.index'));

    $response->assertRedirect(route('login'));
});

test('user can create a code list', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('code-lists.store'), [
            'key' => 'customer_type',
            'name' => 'Customer Type',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseHas('code_lists', [
        'key' => 'customer_type',
        'name' => 'Customer Type',
    ]);
});

test('user can update a code list', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('code-lists.update', $codeList), [
            'key' => 'delivery_status',
            'name' => 'Delivery Status',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseHas('code_lists', [
        'id' => $codeList->id,
        'key' => 'delivery_status',
        'name' => 'Delivery Status',
    ]);
});

test('user can delete a code list and its items', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();
    $item = CodeListItem::factory()->for($codeList)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('code-lists.destroy', $codeList));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseMissing('code_lists', ['id' => $codeList->id]);
    $this->assertDatabaseMissing('code_list_items', ['id' => $item->id]);
});

test('user can create an item in a code list', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('code-lists.items.store', $codeList), [
            'code' => 'pending',
            'label' => 'Pending',
            'sort_order' => 15,
            'active' => 1,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseHas('code_list_items', [
        'code_list_id' => $codeList->id,
        'code' => 'pending',
        'label' => 'Pending',
        'sort_order' => 15,
        'active' => true,
    ]);
});

test('item code must be unique inside the same code list', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();
    CodeListItem::factory()->for($codeList)->create(['code' => 'open']);

    $response = $this
        ->actingAs($user)
        ->from(route('code-lists.index'))
        ->post(route('code-lists.items.store', $codeList), [
            'code' => 'open',
            'label' => 'Open copy',
            'sort_order' => 20,
            'active' => 1,
        ]);

    $response
        ->assertSessionHasErrors('code')
        ->assertRedirect(route('code-lists.index'));
});

test('user can reuse the same item code in a different list', function () {
    $user = User::factory()->create();
    $firstList = CodeList::factory()->create();
    $secondList = CodeList::factory()->create();
    CodeListItem::factory()->for($firstList)->create(['code' => 'open']);

    $response = $this
        ->actingAs($user)
        ->post(route('code-lists.items.store', $secondList), [
            'code' => 'open',
            'label' => 'Open',
            'sort_order' => 10,
            'active' => 1,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseHas('code_list_items', [
        'code_list_id' => $secondList->id,
        'code' => 'open',
    ]);
});

test('user can update a code list item', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();
    $item = CodeListItem::factory()->for($codeList)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('code-lists.items.update', [$codeList, $item]), [
            'code' => 'approved',
            'label' => 'Approved',
            'sort_order' => 99,
            'active' => 0,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseHas('code_list_items', [
        'id' => $item->id,
        'code' => 'approved',
        'label' => 'Approved',
        'sort_order' => 99,
        'active' => false,
    ]);
});

test('user cannot update item through another list path', function () {
    $user = User::factory()->create();
    $firstList = CodeList::factory()->create();
    $secondList = CodeList::factory()->create();
    $item = CodeListItem::factory()->for($firstList)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('code-lists.items.update', [$secondList, $item]), [
            'code' => 'approved',
            'label' => 'Approved',
            'sort_order' => 20,
            'active' => 1,
        ]);

    $response->assertNotFound();
});

test('user can delete a code list item', function () {
    $user = User::factory()->create();
    $codeList = CodeList::factory()->create();
    $item = CodeListItem::factory()->for($codeList)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('code-lists.items.destroy', [$codeList, $item]));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code-lists.index'));

    $this->assertDatabaseMissing('code_list_items', ['id' => $item->id]);
});
