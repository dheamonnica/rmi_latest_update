<?php
use App\Http\Controllers\Admin\TargetController;
use Illuminate\Support\Facades\Route;

Route::get('target', [
    TargetController::class,
    'index'
])->name('admin.target.index');
Route::get('getTargetsTables', [
    TargetController::class,
    'getTargetsTables'
])->name('admin.target.getTargetsTables')->middleware('ajax');
Route::get('create', [
    TargetController::class,
    'create'
])->name('admin.target.create');

Route::put('update/{target}', [TargetController::class, 'update'])->name('admin.target.update');

Route::get('edit/{target}', [TargetController::class, 'edit'])->name('admin.target.edit');

Route::get('show', [
    TargetController::class,
    'show'
])->name('admin.target.show');

Route::delete('target/{target}/trash', [TargetController::class, 'trash'])
    ->name('admin.target.trash');

Route::resource('target', TargetController::class);