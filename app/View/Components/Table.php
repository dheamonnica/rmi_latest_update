<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $options;
    public $header;
    public $dataBody;

    public function __construct( $options = [], $header = [],$dataBody = [])
    {
        $this->options = $options;
        $this->header = $header;
        $this->dataBody = $dataBody;
    }
    public function render()
    {
        return view('components.table');
    }
}
