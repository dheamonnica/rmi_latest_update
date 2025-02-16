<?php

use App\Http\Controllers\Admin\OrderCancellationController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('order/{order}/invoice', [OrderController::class, 'invoice'])->name('order.invoice');
// Route::post('order/massTrash', [OrderController::class, 'massTrash'])->name('order.massTrash')->middleware('demoCheck');

// Route::post('order/massDestroy', [OrderController::class, 'massDestroy'])->name('order.massDestroy')->middleware('demoCheck');

Route::delete('order/emptyTrash', [OrderController::class, 'emptyTrash'])->name('order.emptyTrash');

Route::get('order/{order}/adminNote', [OrderController::class, 'adminNote'])->name('order.adminNote');

Route::put('order/{order}/adminNote', [OrderController::class, 'saveAdminNote'])->name('order.saveAdminNote');

Route::put('order/{order}/saveDueDatePayment', [OrderController::class, 'saveDueDatePayment'])->name('order.saveDueDatePayment');

Route::delete('order/{order}/archive', [OrderController::class, 'archive'])->name('order.archive'); // order move to trash

Route::get('/{order}/details', [OrderController::class, 'show'])->name('details'); // order Details

// Bulk operations

Route::get('/getOrder/{paymentStatus}/{orderStatus}',[OrderController::class, 'showBulkProcess'])->name('bulkorder_process')->middleware('ajax'); // Bulk order process table
Route::get('/getOrderReport',[OrderController::class, 'getOrderReport'])->name('getOrderReport')->middleware('ajax'); // Bulk order process table

Route::post('order/assignPaymentStatus/{assign}', [OrderController::class, 'massAssignPaymentStatus'])->name('order.assignPaymentStatus');

Route::post('order/assignOrderStatus/{status}', [OrderController::class, 'massAssignOrderStatus'])->name('order.assignOrderStatus');

Route::post('order/downloadSelected',[OrderController::class, 'downloadSelected'])->name('order.downloadSelected');

//pickup orders
Route::get('pickup', [OrderController::class, 'index'])->name('pickup'); // pickup orders

// Cancellation routes
Route::get('order/{order}/cancel', [OrderCancellationController::class, 'create'])->name('cancellation.create');

Route::put('order/{order}/cancel', [OrderCancellationController::class, 'cancel'])->name('order.cancel');

Route::get('cancellation', [OrderCancellationController::class, 'index'])->name('order.cancellation');

Route::put('cancellation/{order}/{action}', [OrderCancellationController::class, 'handleCancellationRequest'])->name('cancellation.handle');

Route::get('order/{order}/restore', [OrderController::class, 'restore'])->name('order.restore');

Route::get('order/searchCustomer', [OrderController::class, 'searchCustomer'])->name('order.searchCustomer');

Route::get('order/{order}/fulfill', [OrderController::class, 'fulfillment'])->name('order.fulfillment');

Route::get('order/{order}/deliveredConfirmation', [OrderController::class, 'deliveredConfirmation'])->name('order.deliveredConfirmation');

Route::put('order/{order}/fulfill', [OrderController::class, 'fulfill'])->name('order.fulfill');

Route::put('order/{order}/deliveredConfirmed', [OrderController::class, 'deliveredConfirmed'])->name('order.deliveredConfirmed');

Route::put('order/{order}/updateOrderStatus', [OrderController::class, 'updateOrderStatus'])->name('order.updateOrderStatus');

Route::put('order/{order}/setAsDelivered', [OrderController::class, 'setAsDelivered'])->name('order.setAsDelivered');

Route::put('order/{order}/setAsPacked', [OrderController::class, 'setAsPacked'])->name('order.setAsPacked');

Route::put('order/{order}/togglePaymentStatus', [OrderController::class, 'togglePaymentStatus'])->name('order.togglePaymentStatus');

Route::get('{order}/deliveryboys', [
  OrderController::class, 'deliveryBoys'
])->name('deliveryboys');

Route::post('{order}/deliveryboy/assign', [
  OrderController::class, 'assignDeliveryBoy'
])->name('deliveryboy.assign');

Route::get('order-report', [
  OrderController::class, 'exportIndex'
])->name('order.exportIndex');

Route::get('order-payment-document', [
  OrderController::class, 'paymentDocument'
])->name('order.paymentDocument');

Route::get('/getOrderPaymentDocReport',[OrderController::class, 'getOrderPaymentDocReport'])->name('getOrderPaymentDocReport')->middleware('ajax');

Route::get('order-payment-document-edit/{order}', [
  OrderController::class, 'orderPaymentEdit'
])->name('order.orderPaymentEdit');

Route::put('updateDocPayment/{order}', [OrderController::class, 'updateOrderPayment'])->name('order.updateDocPayment');

Route::get('order-form', [
  OrderController::class, 'orderForm'
])->name('order.orderForm');

Route::get('/getOrderForm',[OrderController::class, 'getOrderForm'])->name('getOrderForm')->middleware('ajax');

Route::get('order-form-edit/{order}', [
  OrderController::class, 'orderFormEdit'
])->name('order.orderFormEdit');

Route::put('updateForm/{order}', [OrderController::class, 'updateForm'])->name('order.updateForm');

Route::resource('order', OrderController::class)->except('update');