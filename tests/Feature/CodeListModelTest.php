<?php

use App\Models\CodeList;
use App\Models\CodeListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads code list items through the relationship', function () {
    $codeList = CodeList::factory()->create();
    CodeListItem::factory()->for($codeList)->count(2)->create();

    expect($codeList->items)->toHaveCount(2);
});

it('links a code list item to its parent list', function () {
    $codeList = CodeList::factory()->create();
    $item = CodeListItem::factory()->for($codeList)->create();

    expect($item->codeList)->not->toBeNull()
        ->and($item->codeList->is($codeList))->toBeTrue();
});

it('casts active and sort order attributes', function () {
    $item = CodeListItem::factory()->create([
        'active' => 1,
        'sort_order' => '7',
    ]);

    expect($item->active)->toBeBool()->toBeTrue()
        ->and($item->sort_order)->toBeInt()->toBe(7);
});
