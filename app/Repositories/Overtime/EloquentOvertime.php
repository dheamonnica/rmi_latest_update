<?php

namespace App\Repositories\Overtime;

use App\Models\Overtime;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentOvertime extends EloquentRepository implements BaseRepository, OvertimeRepository
{
    protected $model;

    public function __construct(Overtime $overtime)
    {
        $this->model = $overtime;
    }

    public function all()
    {
        return Overtime::all();
    }

    public function find($id)
    {
        return Overtime::find($id);
    }

    public function store(Request $request)
    {
        $overtime = parent::store($request);
        if ($request->hasFile('picture')) {
            $overtime->saveImage($request->file('picture'));
        }

        return $overtime;
    }

    public function updateStatusApprove(Request $request, $overtime)
    {
        $overtime->approved_at = date("Y-m-d G:i:s");
        $overtime->approved_by = Auth::user()->id;
        $overtime->updated_at = date("Y-m-d G:i:s");
        $overtime->updated_by = Auth::user()->id;
        $overtime->status = 1;

        return $overtime->save();
    }
}
