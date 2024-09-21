<?php
use App\Http\Controllers\Admin\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('payroll', [
    PayrollController::class,
    'index'
])->name('admin.payroll.index');
Route::get('payroll-report', [
    PayrollController::class,
    'report'
])->name('admin.payroll.report');
Route::get('getPayrolls', [
    PayrollController::class,
    'getPayrolls'
])->name('admin.payroll.getPayrolls')->middleware('ajax');
Route::get('getReportPayroll', [
    PayrollController::class,
    'getReportPayroll'
])->name('admin.payroll.getReportPayroll')->middleware('ajax');
Route::get('create', [
    PayrollController::class,
    'create'
])->name('admin.payroll.create');
Route::put('update/{payroll}', [PayrollController::class, 'update'])->name('admin.payroll.update');
Route::get('edit/{payroll}', [PayrollController::class, 'edit'])->name('admin.payroll.edit');
Route::delete('payroll/{payroll}/trash', [PayrollController::class, 'trash'])
    ->name('admin.payroll.trash');
Route::resource('payroll', PayrollController::class);