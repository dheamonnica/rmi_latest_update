<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InfoBoxes extends Component
{

    public $pending_verifications= 0;
    public $pending_approvals= 0;
    public $dispute_count= 0;
    public $last_60days_dispute_count= 0;
    public $last_30days_dispute_count= 0;
        
    public function render()
    {
        return view('livewire.info-boxes');
    }
}
