<?php

use App\Http\Controllers\CodeListController;
use App\Http\Controllers\CodeListItemController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('{user}/edit', 'edit')->name('edit');
        Route::put('{user}', 'update')->name('update');
    });

    Route::prefix('code-lists')->name('code-lists.')->scopeBindings()->group(function () {
        Route::get('/', [CodeListController::class, 'index'])->name('index');
        Route::post('/', [CodeListController::class, 'store'])->name('store');
        Route::patch('{codeList}', [CodeListController::class, 'update'])->name('update');
        Route::delete('{codeList}', [CodeListController::class, 'destroy'])->name('destroy');

        Route::post('{codeList}/items', [CodeListItemController::class, 'store'])->name('items.store');
        Route::patch('{codeList}/items/{codeListItem}', [CodeListItemController::class, 'update'])->name('items.update');
        Route::delete('{codeList}/items/{codeListItem}', [CodeListItemController::class, 'destroy'])->name('items.destroy');
    });

    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactsController::class, 'index'])->name('index');
        Route::post('/', [ContactsController::class, 'store'])->name('store');
        Route::get('template-csv', [ContactsController::class, 'downloadTemplateCsv'])->name('template-csv');
        Route::post('import-csv', [ContactsController::class, 'importCsv'])->name('import-csv');
        Route::get('{contact}/edit', [ContactsController::class, 'edit'])->name('edit');
        Route::put('{contact}', [ContactsController::class, 'update'])->name('update');
        Route::delete('{contact}', [ContactsController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
