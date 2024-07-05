<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $count;
    public $count2;
    public $options;

    public function __construct($count ,$count2, $options = [])
    {
        $this->count = $count;
        $this->count2 = $count2;
        $this->options = $options;
    }

    public function render()
    {
        // dd($this->options);
        return view('components.dashboard-card');
    }
}
