<?php

use App\Http\Controllers\Admin\CatalogProductController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductUploadController;
use Illuminate\Support\Facades\Route;

// Bulk upload routes
Route::get('product/upload/downloadCategorySlugs', [ProductUploadController::class, 'downloadCategorySlugs'])->name('product.downloadCategorySlugs');

Route::get('product/upload/downloadTemplate', [ProductUploadController::class, 'downloadTemplate'])->name('product.downloadTemplate');

Route::get('product/upload', [ProductUploadController::class, 'showForm'])->name('product.bulk');

Route::post('product/upload', [ProductUploadController::class, 'upload'])->name('product.upload');

Route::post('product/import', [ProductUploadController::class, 'import'])->name('product.import');

Route::post('product/downloadFailedRows', [ProductUploadController::class, 'downloadFailedRows'])->name('product.downloadFailedRows');

// Catalog Product routes

    Route::post('product/massTrash', [CatalogProductController::class, 'massTrash'])->name('product.massTrash');

    Route::post('product/massDestroy', [CatalogProductController::class, 'massDestroy'])->name('product.massDestroy');

    Route::delete('product/emptyTrash', [CatalogProductController::class, 'emptyTrash'])->name('product.emptyTrash');

    Route::delete('product/{product}/trash', [CatalogProductController::class, 'trash'])->name('product.trash'); // product move to trash

    Route::get('product/{product}/restore', [CatalogProductController::class, 'restore'])->name('product.restore');

    Route::post('product/store', [CatalogProductController::class, 'store'])->name('product.store')->middleware('ajax');

    Route::post('product/{product}/update', [CatalogProductController::class, 'update'])->name('product.update')->middleware('ajax');

    Route::get('product/getProducts', [CatalogProductController::class, 'getProducts'])->name('product.getMore');

    Route::resource('product', CatalogProductController::class)->except('store', 'update');

//if (!is_catalog_enabled()) {
//
//// Product routes
//    Route::post('product/massTrash', [ProductController::class, 'massTrash'])->name('product.massTrash');
//
//    Route::post('product/massDestroy', [ProductController::class, 'massDestroy'])->name('product.massDestroy');
//
//    Route::delete('product/emptyTrash', [ProductController::class, 'emptyTrash'])->name('product.emptyTrash');
//
//    Route::delete('product/{product}/trash', [ProductController::class, 'trash'])->name('product.trash'); // product move to trash
//
//    Route::get('product/{product}/restore', [ProductController::class, 'restore'])->name('product.restore');
//
//    Route::post('product/store', [ProductController::class, 'store'])->name('product.store')->middleware('ajax');
//
//    Route::post('product/{product}/update', [ProductController::class, 'update'])->name('product.update')->middleware('ajax');
//
//    Route::get('product/getProducts', [ProductController::class, 'getProducts'])->name('product.getMore');
//
//    Route::get('product/get-attributes-by-categories', [ProductController::class, 'getAttributesByCategories'])->name('product.getAttributesByCategories');
//
//    Route::get('product/{product}/addVariant', [ProductController::class, 'singleVariantForm'])->name('product.addVariant');
//
//    Route::post('product/{product}/saveVariant', [ProductController::class, 'saveSingleVariant'])->name('product.saveVariant');
//
//    Route::get('product/getCombinations', [ProductController::class, 'getCombinations'])->name('product.getCombinations');
//
//    Route::resource('product', ProductController::class)->except('store', 'update');
//
//}
