<?php

use App\Http\Controllers\Admin\PurchasingController;
use Illuminate\Support\Facades\Route;


Route::post('purchasing/massTrash', [PurchasingController::class, 'massTrash'])->name('purchasing.massTrash');

Route::post('purchasing/massDestroy', [PurchasingController::class, 'massDestroy'])->name('purchasing.massDestroy');

Route::delete('purchasing/emptyTrash', [PurchasingController::class, 'emptyTrash'])->name('purchasing.emptyTrash');

Route::delete('purchasing/{purchasing}/trash', [PurchasingController::class, 'trash'])->name('purchasing.trash'); // purchasing move to trash

Route::get('purchasing/{purchasing}/restore', [PurchasingController::class, 'restore'])->name('purchasing.restore');

Route::get('purchasing/{purchasing}/show', [PurchasingController::class, 'show'])->name('purchasing.show');

Route::get('purchasing/create', [PurchasingController::class, 'create'])->name('purchasing.create');

Route::get('purchasing/invoice/{id}', [PurchasingController::class, 'invoice'])->name('purchasing.invoice');

Route::post('purchasing/store', [PurchasingController::class, 'store'])->name('purchasing.store');

Route::post('purchasing/{purchasing}/update', [PurchasingController::class, 'update'])->name('purchasing.update')->middleware('ajax');

Route::post('purchasing/{purchasing}/setShippingStatus', [PurchasingController::class, 'setShippingStatus'])->name('purchasing.setShippingStatus');

Route::post('purchasing/{purchasing}/setPrice', [PurchasingController::class, 'setPrice'])->name('purchasing.setPrice');

Route::post('purchasing/updateManufacture', [PurchasingController::class, 'updateManufacture'])->name('purchasing.updateManufacture');

Route::post('purchasing/massManufacture', [PurchasingController::class, 'massManufacture'])->name('purchasing.assignManufacture');

Route::post('purchasing/assignManufacture', [PurchasingController::class, 'assignManufacture'])->name('purchasing.assignMassManufacture');

Route::post('purchasing/generateInvoice', [PurchasingController::class, 'generateInvoice'])->name('purchasing.generateInvoice');

Route::get('purchasing/getPurchasing', [PurchasingController::class, 'getPurchasing'])->name('purchasing.getMore');

Route::get('purchasing/getRequestComplete', [PurchasingController::class, 'getRequestComplete'])->name('purchasing.getRequestComplete');

Route::get('purchasing/getRequest', [PurchasingController::class, 'getRequest'])->name('purchasing.getMoreRequest');

Route::resource('purchasing', PurchasingController::class)->except('store', 'show','update');