<?php
use App\Http\Controllers\Admin\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('loan', [
    LoanController::class,
    'index'
])->name('admin.loan.index');
Route::get('loan-report', [
    LoanController::class,
    'report'
])->name('admin.loan.report');
Route::get('getLoans', [
    LoanController::class,
    'getLoans'
])->name('admin.loan.getLoans')->middleware('ajax');
Route::get('create', [
    LoanController::class,
    'create'
])->name('admin.loan.create');
Route::put('update/{loan}', [LoanController::class, 'update'])->name('admin.loan.update');
Route::put('loan/{loan}/setApprove', [LoanController::class, 'setApprove'])->name('loan.setApprove');
Route::get('edit/{loan}', [LoanController::class, 'edit'])->name('admin.loan.edit');
Route::delete('loan/{loan}/trash', [LoanController::class, 'trash'])
    ->name('admin.loan.trash');
Route::resource('loan', LoanController::class);