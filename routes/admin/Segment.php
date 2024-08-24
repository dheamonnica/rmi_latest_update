<?php
use App\Http\Controllers\Admin\SegmentController;
use Illuminate\Support\Facades\Route;

Route::get('segment', [
    SegmentController::class,
    'index'
])->name('admin.segment.index');
Route::get('create', [
    SegmentController::class,
    'create'
])->name('admin.segment.create');
Route::get('getSegments', [
    SegmentController::class,
    'getSegments'
])->name('admin.segment.getSegments')->middleware('ajax');

Route::put('update/{segment}', [SegmentController::class, 'update'])->name('admin.segment.update');

Route::get('edit/{segment}', [SegmentController::class, 'edit'])->name('admin.segment.edit');

Route::delete('segment/{segment}/trash', [SegmentController::class, 'trash'])
    ->name('admin.segment.trash');

Route::resource('segment', SegmentController::class);