<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PoChartTimeFilter extends Component
{
    public $chart;
    
    public function render()
    {
        return view('livewire.po-chart-time-filter');
    }
}
