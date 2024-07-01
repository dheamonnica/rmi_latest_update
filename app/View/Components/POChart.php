<?php

namespace App\View\Components;

use Illuminate\View\Component;

class POChart extends Component
{
    public $chartData;
    public $options;
    
    public function __construct($chartData = [], $options = [])
    {
        $this->chartData = $chartData;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.p-o-chart');
    }
}
