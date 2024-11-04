<?php

namespace App\Repositories\Offering;

use App\Models\Offering;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentOffering extends EloquentRepository implements BaseRepository, OfferingRepository
{
    protected $model;

    public function __construct(Offering $offering)
    {
        $this->model = $offering;
    }

    public function all()
    {
        return Offering::all();
    }

    public function find($id)
    {
        return Offering::find($id);
    }

    public function store(Request $request)
    {
        $offering = parent::store($request);

        return $offering;
    }

    public function getDatabyUser($user_id)
    {
        return DB::table('offering')->where('created_by', $user_id)->get();
    }

    public function updateStatusApprove(Request $request, $offering)
    {
        if (!$offering instanceof Offering) {
            $offering = $this->model->find($offering);
        }

        $offering->approved_at = date("Y-m-d G:i:s");
        $offering->approved_by = Auth::user()->id;
        $offering->updated_at = date("Y-m-d G:i:s");
        $offering->updated_by = Auth::user()->id;
        $offering->status = 1;

        return $offering->save();
    }
}
