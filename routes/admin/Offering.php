<?php

use App\Http\Controllers\Admin\OfferingController;
use Illuminate\Support\Facades\Route;

Route::get('index', [
    OfferingController::class,
    'index'
])->name('admin.offering.index');

Route::get('create', [
    OfferingController::class,
    'create'
])->name('admin.offering.create');

Route::put('update/{offering}', [OfferingController::class, 'update'])->name('admin.offering.update');

Route::get('edit/{offering}', [OfferingController::class, 'edit'])->name('admin.offering.edit');

Route::get('show', [
    OfferingController::class,
    'show'
])->name('admin.offering.show');

Route::delete('offering/{offering}/trash', [OfferingController::class, 'trash'])
    ->name('admin.offering.trash');

Route::get('getOfferings', [
    OfferingController::class,
    'getOfferings'
])->name('admin.offering.getOfferings')->middleware('ajax');

Route::put('offering/{offering}/setApprove', [OfferingController::class, 'setApprove'])->name('offering.setApprove');

Route::resource('offering', OfferingController::class);
