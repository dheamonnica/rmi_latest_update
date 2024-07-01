<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InfoCard extends Component
{
    public $count;
    public $options;

    public function __construct($count = 0, $options = [])
    {
        $this->count = $count;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.info-card');
    }
}
