<?php
use App\Http\Controllers\Admin\OvertimeController;
use Illuminate\Support\Facades\Route;

Route::get('overtime', [
    OvertimeController::class,
    'index'
])->name('admin.overtime.index');
Route::get('overtime-report', [
    OvertimeController::class,
    'report'
])->name('admin.overtime.report');
Route::get('getOvertimes', [
    OvertimeController::class,
    'getOvertimes'
])->name('admin.overtime.getOvertimes')->middleware('ajax');
Route::get('create', [
    OvertimeController::class,
    'create'
])->name('admin.overtime.create');
Route::put('update/{overtime}', [OvertimeController::class, 'update'])->name('admin.overtime.update');
Route::get('edit/{overtime}', [OvertimeController::class, 'edit'])->name('admin.overtime.edit');
Route::put('overtime/{overtime}/setApprove', [OvertimeController::class, 'setApprove'])->name('overtime.setApprove');
Route::delete('overtime/{overtime}/trash', [OvertimeController::class, 'trash'])
    ->name('admin.overtime.trash');
Route::resource('overtime', OvertimeController::class);