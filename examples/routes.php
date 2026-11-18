<?php

use App\Http\Controllers\LeaveTableController;

// #region catalog
use Illuminate\Support\Facades\Route;
use Rmunate\EasyDatatable\EasyDataTable;

Route::view('/examples/catalog', 'examples.catalog');

Route::get('/examples/catalog/data', function () {
    return EasyDataTable::init()->fromData([
        ['name' => 'Desk lamp', 'stock' => 12],
        ['name' => 'Monitor stand', 'stock' => 4],
        ['name' => 'USB-C adapter', 'stock' => 0],
    ])->response();
})->name('examples.catalog.data');
// #endregion catalog

// #region leave-routes
Route::middleware('auth')->prefix('examples/leave')->name('examples.leave.')->group(function () {
    Route::view('/client', 'examples.leave', ['serverSide' => false]);
    Route::view('/server', 'examples.leave', ['serverSide' => true]);
    Route::get('/client-data', [LeaveTableController::class, 'client'])->name('client-data');
    Route::get('/server-data', [LeaveTableController::class, 'server'])->name('server-data');
    Route::get('/review/{id}', [LeaveTableController::class, 'review'])->whereNumber('id')->name('review');
});
// #endregion leave-routes
