<?php
use App\Http\Controllers\Admin\BudgetController;
use Illuminate\Support\Facades\Route;

Route::get('budget', [
    BudgetController::class,
    'index'
])->name('admin.budget.index');
Route::get('getBudgets', [
    BudgetController::class,
    'getBudgets'
])->name('admin.budget.getBudgets')->middleware('ajax');
Route::get('budget/report', [
    BudgetController::class,
    'report'
])->name('budget.report');
Route::get('getBudgetsReport', [
    BudgetController::class,
    'getBudgetsReport'
])->name('admin.budget.getBudgetsReport')->middleware('ajax');
Route::get('create', [
    BudgetController::class,
    'create'
])->name('admin.budget.create');

Route::put('update/{budget}', [BudgetController::class, 'update'])->name('admin.budget.update');

Route::get('edit/{budget}', [BudgetController::class, 'edit'])->name('admin.budget.edit');

// Route::get('show', [
//     BudgetController::class,
//     'show'
// ])->name('admin.budget.show');

Route::delete('budget/{budget}/trash', [BudgetController::class, 'trash'])
    ->name('admin.budget.trash');


Route::put('budget/{budget}/setApprove', [BudgetController::class, 'setApprove'])->name('budget.setApprove');

// administrator
Route::get('budget/report-administrator', [
    BudgetController::class,
    'reportAdministrator'
])->name('budget.reportAdministrator');

Route::get('getBudgetsTablesReportAdministrator', [
    BudgetController::class,
    'getBudgetsTablesReportAdministrator'
])->name('admin.budget.getBudgetsTablesReportAdministrator')->middleware('ajax');

Route::get('getBudgetsTablesExpandClientAdministrator', [
    BudgetController::class,
    'getBudgetsTablesExpandClientAdministrator'
])->name('admin.budget.getBudgetsTablesExpandClientAdministrator')->middleware('ajax');

Route::get('getBudgetsTablesExpandAdministrator', [
    BudgetController::class,
    'getBudgetsTablesExpandAdministrator'
])->name('admin.budget.getBudgetsTablesExpandAdministrator')->middleware('ajax');

Route::get('getBudgetTablesExpandAdministrator', [
    BudgetController::class,
    'getBudgetTablesExpandAdministrator'
])->name('admin.budget.getBudgetTablesExpandAdministrator')->middleware('ajax');

Route::resource('budget', BudgetController::class);