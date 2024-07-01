<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $count;
    public $count_plus;
    public $options;

    public function __construct($count ,$count_plus = 0, $options = [])
    {
        $this->count = $count;
        $this->count_plus = $count_plus;
        $this->options = $options;
    }

    public function render()
    {
        // dd($this->options);
        return view('components.dashboard-card');
    }
}
