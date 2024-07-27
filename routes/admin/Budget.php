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

Route::resource('budget', BudgetController::class);