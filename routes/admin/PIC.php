<?php
use App\Http\Controllers\Admin\PICController;
use Illuminate\Support\Facades\Route;

Route::get('create/{pic}', [
    PICController::class,
    'create'
])->name('admin.pic.create');

Route::resource('pic', PICController::class);