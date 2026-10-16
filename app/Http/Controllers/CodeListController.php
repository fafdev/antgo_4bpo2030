<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCodeListRequest;
use App\Http\Requests\UpdateCodeListRequest;
use App\Models\CodeList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CodeListController extends Controller
{
    /**
     * Display a listing of code lists and their items.
     */
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));

        $codeLists = CodeList::query()
            ->with([
                'items' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('code-lists/index', [
            'codeLists' => $codeLists,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Store a newly created code list.
     */
    public function store(StoreCodeListRequest $request): RedirectResponse
    {
        CodeList::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list created.')]);

        return to_route('code-lists.index');
    }

    /**
     * Update the specified code list.
     */
    public function update(UpdateCodeListRequest $request, CodeList $codeList): RedirectResponse
    {
        $codeList->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list updated.')]);

        return to_route('code-lists.index');
    }

    /**
     * Remove the specified code list.
     */
    public function destroy(CodeList $codeList): RedirectResponse
    {
        $codeList->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Code list deleted.')]);

        return to_route('code-lists.index');
    }
}
