<?php

namespace App\Repositories\CRM;

use App\Models\CRM;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentCRM extends EloquentRepository implements BaseRepository, CRMRepository
{
    protected $model;

    public function __construct(CRM $crm)
    {
        $this->model = $crm;
    }

    public function all()
    {
        return CRM::all();
    }

    public function find($id)
    {
        return CRM::find($id);
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
        if (!$crm instanceof CRM) {
            $crm = $this->model->find($crm);
        }

        $crm->verified_at = date("Y-m-d G:i:s");
        $crm->verified_by = Auth::user()->id;
        $crm->updated_at = date("Y-m-d G:i:s");
        $crm->updated_by = Auth::user()->id;

        return $crm->save();
    }
}
