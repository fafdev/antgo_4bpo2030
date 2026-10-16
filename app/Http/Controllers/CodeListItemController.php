<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCodeListItemRequest;
use App\Http\Requests\UpdateCodeListItemRequest;
use App\Models\CodeList;
use App\Models\CodeListItem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class CodeListItemController extends Controller
{
    /**
     * Store a newly created item under the given list.
     */
    public function store(StoreCodeListItemRequest $request, CodeList $codeList): RedirectResponse
    {
        $codeList->items()->create([
            ...$request->validated(),
            'active' => $request->boolean('active'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list item created.')]);

        return to_route('code-lists.index');
    }

    /**
     * Update the specified code list item.
     */
    public function update(UpdateCodeListItemRequest $request, CodeList $codeList, CodeListItem $codeListItem): RedirectResponse
    {
        $codeListItem->update([
            ...$request->validated(),
            'active' => $request->boolean('active'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list item updated.')]);

        return to_route('code-lists.index');
    }

    /**
     * Remove the specified code list item.
     */
    public function destroy(CodeList $codeList, CodeListItem $codeListItem): RedirectResponse
    {
        $codeListItem->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list item deleted.')]);

        return to_route('code-lists.index');
    }
}
