<?php
use App\Http\Controllers\Admin\AbsenceController;
use Illuminate\Support\Facades\Route;

// Absence
Route::get('absence', [
    AbsenceController::class,
    'index'
])->name('admin.absence.index');
Route::get('getAbsences', [
    AbsenceController::class,
    'getAbsences'
])->name('admin.absence.getAbsences')->middleware('ajax');
Route::get('checkIfUserHasClockIn', [
    AbsenceController::class,
    'checkIfUserHasClockIn'
])->name('admin.absence.checkIfUserHasClockIn')->middleware('ajax');
Route::post('admin/absence/uploadPhotoClockIn/save', [
    AbsenceController::class, 'uploadPhotoClockIn'
])->name('admin.absence.uploadPhotoClockIn.save');
Route::post('admin/absence/uploadPhotoClockOut/save', [
    AbsenceController::class, 'uploadPhotoClockOut'
])->name('admin.absence.uploadPhotoClockOut.save');
Route::get('checkIfUserHasClockOut', [
    AbsenceController::class,
    'checkIfUserHasClockOut'
])->name('admin.absence.checkIfUserHasClockOut')->middleware('ajax');
Route::put('clockOut', [
    AbsenceController::class,
    'clockOut'
])->name('admin.absence.clockOut')->middleware('ajax');
Route::get('create', [
    AbsenceController::class,
    'create'
])->name('admin.absence.create');
Route::put('update/{absence}', [AbsenceController::class, 'update'])->name('admin.absence.update');
Route::put('absence/{absence}/setApprove', [AbsenceController::class, 'setApprove'])->name('absence.setApprove');
Route::get('edit/{absence}', [AbsenceController::class, 'edit'])->name('admin.absence.edit');
Route::delete('absence/{absence}/trash', [AbsenceController::class, 'trash'])
    ->name('admin.absence.trash');
Route::resource('absence', AbsenceController::class);