<?php

namespace App\Repositories\Purchasing;

use Illuminate\Http\Request;

interface PurchasingRepository
{
    public function all($status);

    public function updatePurchasingStatus(Request $request, $status);

    // public function findProduct($id);

    // public function storeWithVariant(Request $request);

    // public function updateQtt(Request $request, $id);

    // public function setAttributes($inventory, $attributes);

    // public function getAttributeList(array $variants);

    // public function confirmAttributes($attributeWithValues);

    // public function storeStockTransfer(Request $request);

    // public function updateStockTransferStatus(Request $request, $stockTransfer);
}
