<?php

namespace App\Repositories\Visit;

use App\Models\Visit;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentVisit extends EloquentRepository implements BaseRepository, VisitRepository
{
    protected $model;

    public function __construct(Visit $crm)
    {
        $this->model = $crm;
    }

    public function all()
    {
        return Visit::all();
    }

    public function find($id)
    {
        return Visit::find($id);
    }

    public function store(Request $request)
    {
        $crm = parent::store($request);
        if ($request->hasFile('picture')) {
            $crm->saveImage($request->file('picture'));
        }

        return $crm;
    }

    public function updateStatusApprove(Request $request, $crm)
    {
        if (!$crm instanceof Visit) {
            $crm = $this->model->find($crm);
        }

        $crm->verified_at = date("Y-m-d G:i:s");
        $crm->verified_by = Auth::user()->id;
        $crm->status = 1;
        $crm->updated_at = date("Y-m-d G:i:s");
        $crm->updated_by = Auth::user()->id;

        return $crm->save();
    }
}
