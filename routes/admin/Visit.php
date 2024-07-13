<?php
use App\Http\Controllers\Admin\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('visit', [
    VisitController::class,
    'index'
])->name('admin.visit.index');
Route::get('getVisitsTables', [
    VisitController::class,
    'getVisitsTables'
])->name('admin.visit.getVisitsTables')->middleware('ajax');
Route::get('create', [
    VisitController::class,
    'create'
])->name('admin.visit.create');

Route::put('update/{visit}', [VisitController::class, 'update'])->name('admin.visit.update');

Route::get('edit/{visit}', [VisitController::class, 'edit'])->name('admin.visit.edit');

Route::get('show', [
    VisitController::class,
    'show'
])->name('admin.visit.show');

Route::delete('visit/{visit}/trash', [VisitController::class, 'trash'])
    ->name('admin.visit.trash');

Route::put('visit/{visit}/setApprove', [VisitController::class, 'setApprove'])->name('visit.setApprove');

Route::resource('visit', VisitController::class);