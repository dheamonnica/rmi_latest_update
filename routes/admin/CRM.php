<?php
use App\Http\Controllers\Admin\CRMController;
use Illuminate\Support\Facades\Route;

Route::get('crm', [
    CRMController::class,
    'index'
])->name('admin.crm.index');
Route::get('crm/data', [
    CRMController::class,
    'data'
])->name('crm.data');
Route::get('getCRMsTables', [
    CRMController::class,
    'getCRMsTables'
])->name('admin.crm.getCRMsTables')->middleware('ajax');
Route::get('getCRMsDataTables', [
    CRMController::class,
    'getCRMsDataTables'
])->name('admin.crm.getCRMsDataTables')->middleware('ajax');
Route::get('create', [
    CRMController::class,
    'create'
])->name('admin.crm.create');

Route::put('update/{crm}', [CRMController::class, 'update'])->name('admin.crm.update');

Route::get('edit/{crm}', [CRMController::class, 'edit'])->name('admin.crm.edit');

Route::get('show', [
    CRMController::class,
    'show'
])->name('admin.crm.show');

Route::delete('crm/{crm}/trash', [CRMController::class, 'trash'])
    ->name('admin.crm.trash');

Route::put('crm/{crm}/setApprove', [CRMController::class, 'setApprove'])->name('crm.setApprove');

Route::resource('crm', CRMController::class);