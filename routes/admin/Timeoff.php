<?php
use App\Http\Controllers\Admin\TimeOffController;
use Illuminate\Support\Facades\Route;

// Timeoff
Route::get('timeoff', [
    TimeOffController::class,
    'index'
])->name('admin.timeoff.index');
Route::get('getTimeOff', [
    TimeOffController::class,
    'getTimeOff'
])->name('admin.timeoff.getTimeOff')->middleware('ajax');
Route::get('create', [
    TimeOffController::class,
    'create'
])->name('admin.timeoff.create');
Route::put('update/{timeoff}', [TimeOffController::class, 'update'])->name('admin.timeoff.update');
Route::put('timeoff/{timeoff}/setApprove', [TimeOffController::class, 'setApprove'])->name('timeoff.setApprove');
Route::get('edit/{timeoff}', [TimeOffController::class, 'edit'])->name('admin.timeoff.edit');
Route::delete('timeoff/{timeoff}/trash', [TimeOffController::class, 'trash'])
    ->name('admin.timeoff.trash');
Route::resource('timeoff', TimeOffController::class);