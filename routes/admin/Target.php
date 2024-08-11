<?php
use App\Http\Controllers\Admin\TargetController;
use Illuminate\Support\Facades\Route;

Route::get('target', [
    TargetController::class,
    'index'
])->name('admin.target.index');
Route::get('target/report', [
    TargetController::class,
    'report'
])->name('target.report');
Route::get('target/report-administrator', [
    TargetController::class,
    'reportAdministrator'
])->name('target.reportAdministrator');
Route::get('getTargetsTables', [
    TargetController::class,
    'getTargetsTables'
])->name('admin.target.getTargetsTables')->middleware('ajax');
Route::get('getTargetsTablesReport', [
    TargetController::class,
    'getTargetsTablesReport'
])->name('admin.target.getTargetsTablesReport')->middleware('ajax');
Route::get('getTargetsTablesExpand', [
    TargetController::class,
    'getTargetsTablesExpand'
])->name('admin.target.getTargetsTablesExpand')->middleware('ajax');
Route::get('getTargetsTablesReportAdministrator', [
    TargetController::class,
    'getTargetsTablesReportAdministrator'
])->name('admin.target.getTargetsTablesReportAdministrator')->middleware('ajax');
Route::get('getTargetsTablesExpandClientAdministrator', [
    TargetController::class,
    'getTargetsTablesExpandClientAdministrator'
])->name('admin.target.getTargetsTablesExpandClientAdministrator')->middleware('ajax');
// getTargetsTablesExpandAdministrator
Route::get('getTargetsTablesExpandAdministrator', [
    TargetController::class,
    'getTargetsTablesExpandAdministrator'
])->name('admin.target.getTargetsTablesExpandAdministrator')->middleware('ajax');
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