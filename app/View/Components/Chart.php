<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Chart extends Component
{
    public $chartData;
    public $options;
    public function __construct($chartData = [], $options = [])
    {
        $this->chartData = $chartData;
        $this->options = $options;
    }
    public function render()
    {
        return view('components.chart');
    }
}
