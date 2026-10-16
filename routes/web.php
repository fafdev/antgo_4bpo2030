<?php

use App\Http\Controllers\CodeListController;
use App\Http\Controllers\CodeListItemController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('code-lists')->name('code-lists.')->scopeBindings()->group(function () {
        Route::get('/', [CodeListController::class, 'index'])->name('index');
        Route::post('/', [CodeListController::class, 'store'])->name('store');
        Route::patch('{codeList}', [CodeListController::class, 'update'])->name('update');
        Route::delete('{codeList}', [CodeListController::class, 'destroy'])->name('destroy');

        Route::post('{codeList}/items', [CodeListItemController::class, 'store'])->name('items.store');
        Route::patch('{codeList}/items/{codeListItem}', [CodeListItemController::class, 'update'])->name('items.update');
        Route::delete('{codeList}/items/{codeListItem}', [CodeListItemController::class, 'destroy'])->name('items.destroy');
    });
});

require __DIR__.'/settings.php';
