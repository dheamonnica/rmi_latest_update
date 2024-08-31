<?php
use App\Http\Controllers\Admin\RequirementController;
use Illuminate\Support\Facades\Route;

Route::get('requirement', [
    RequirementController::class,
    'index'
])->name('admin.requirement.index');
Route::get('create', [
    RequirementController::class,
    'create'
])->name('admin.requirement.create');
Route::get('getRequirements', [
    RequirementController::class,
    'getRequirements'
])->name('admin.requirement.getRequirements')->middleware('ajax');

Route::put('update/{requirement}', [RequirementController::class, 'update'])->name('admin.requirement.update');

Route::get('edit/{requirement}', [RequirementController::class, 'edit'])->name('admin.requirement.edit');

Route::delete('requirement/{requirement}/trash', [RequirementController::class, 'trash'])
    ->name('admin.requirement.trash');

Route::resource('requirement', RequirementController::class);