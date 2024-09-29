<?php
use App\Http\Controllers\Admin\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::get('department', [
    DepartmentController::class,
    'index'
])->name('admin.department.index');
Route::get('department-report', [
    DepartmentController::class,
    'report'
])->name('admin.department.report');
Route::get('getDepartments', [
    DepartmentController::class,
    'getDepartments'
])->name('admin.department.getDepartments')->middleware('ajax');
Route::get('create', [
    DepartmentController::class,
    'create'
])->name('admin.department.create');
Route::put('update/{department}', [DepartmentController::class, 'update'])->name('admin.department.update');
Route::get('edit/{department}', [DepartmentController::class, 'edit'])->name('admin.department.edit');
Route::delete('department/{department}/trash', [DepartmentController::class, 'trash'])
    ->name('admin.department.trash');
Route::resource('department', DepartmentController::class);