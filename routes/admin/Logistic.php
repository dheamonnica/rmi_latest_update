<?php
use App\Http\Controllers\Admin\LogisticController;
use Illuminate\Support\Facades\Route;

Route::get('logistic', [
    LogisticController::class,
    'index'
])->name('admin.logistic.index');
Route::get('getLogistics', [
    LogisticController::class,
    'getLogistics'
])->name('admin.logistic.getLogistics')->middleware('ajax');
Route::get('create', [
    LogisticController::class,
    'create'
])->name('admin.logistic.create');
Route::put('update/{logistic}', [LogisticController::class, 'update'])->name('admin.logistic.update');
Route::get('edit/{logistic}', [LogisticController::class, 'edit'])->name('admin.logistic.edit');
Route::delete('logistic/{logistic}/trash', [LogisticController::class, 'trash'])
    ->name('admin.logistic.trash');
Route::resource('logistic', LogisticController::class);