<?php

namespace App\Repositories\TimeOff;

use App\Models\TimeOff;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentTimeOff extends EloquentRepository implements BaseRepository, TimeOffRepository
{
    protected $model;

    public function __construct(TimeOff $timeoff)
    {
        $this->model = $timeoff;
    }

    public function all()
    {
        return TimeOff::all();
    }

    public function find($id)
    {
        return TimeOff::find($id);
    }

    public function store(Request $request)
    {
        $timeoff = parent::store($request);
        if ($request->hasFile('picture')) {
            $timeoff->saveImage($request->file('picture'));
        }

        return $timeoff;
    }
    public function updateStatusApprove(Request $request, $timeoff)
    {
        $timeoff->approved_at = date("Y-m-d G:i:s");
        $timeoff->approved_by = Auth::user()->id;
        $timeoff->updated_at = date("Y-m-d G:i:s");
        $timeoff->updated_by = Auth::user()->id;
        $timeoff->status = 1;

        return $timeoff->save();
    }
}
