<?php
use App\Http\Controllers\Admin\LoanController;
use Illuminate\Support\Facades\Route;

// LOAN
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

// LOAN PAYMENT
Route::get('loan-payment', [
    LoanController::class,
    'payment'
])->name('admin.loan.payment');
Route::get('getLoanPayments', [
    LoanController::class,
    'getLoanPayments'
])->name('admin.loan.getLoanPayments')->middleware('ajax');
Route::get('create-loan-payment', [
    LoanController::class,
    'createLoanPayment'
])->name('loan.payment.create');
Route::post('store-payment', [LoanController::class, 'storeLoanPayment'])->name('loan.payment.store');
Route::put('update-payment/{loan}', [LoanController::class, 'updatePaymentLoan'])->name('loan.payment.update');
Route::put('loan-payment/{loan}/setApprove', [LoanController::class, 'setApprovePaymentLoan'])->name('loan.payment.setApprove');
Route::get('edit-payment/{loan}', [LoanController::class, 'editPaymentLoan'])->name('loan.payment.edit');
Route::delete('loan-payment/{loan}/trash', [LoanController::class, 'trashPaymentLoan'])
    ->name('admin.loan.payment.trash');


Route::get('loan-report', [
    LoanController::class,
    'report'
])->name('admin.loan.report');
// API
Route::get('getLoanAndPaymentData', [
    LoanController::class,
    'getLoanAndPaymentData'
])->name('admin.loan.getLoanAndPaymentData')->middleware('ajax');
Route::get('getDataLoanReportFirst', [
    LoanController::class,
    'getDataLoanReportFirst'
])->name('admin.loan.getDataLoanReportFirst')->middleware('ajax');
Route::get('getDataLoanReportSecond', [
    LoanController::class,
    'getDataLoanReportSecond'
])->name('admin.loan.getDataLoanReportSecond')->middleware('ajax');
Route::resource('loan', LoanController::class);