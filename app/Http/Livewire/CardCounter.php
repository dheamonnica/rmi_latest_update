<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CardCounter extends Component
{
    public $customer_count = 0;
    public $new_customer_last_30_days = 0;

    public $merchant_count = 0;
    public $new_merchant_last_30_days = 0;

    public $total_order_count = 0;
    public $todays_all_order_count = 0;
    public $yesterdays_all_order_count = 0;

    public $todays_sale_amount = 0;
    public $yesterdays_sale_amount = 0;

    public function render()
    {
        return view('livewire.card-counter');
    }
}
