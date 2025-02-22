<?php

namespace App\Repositories\Logistic;

use App\Models\Logistic;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentLogistic extends EloquentRepository implements BaseRepository, LogisticRepository
{
    protected $model;

    public function __construct(Logistic $logistic)
    {
        $this->model = $logistic;
    }

    public function all()
    {
        return Logistic::all();
    }

    public function find($id)
    {
        return Logistic::find($id);
    }

    public function store(Request $request)
    {
        $logistic = parent::store($request);
        if ($request->hasFile('picture')) {
            $logistic->saveImage($request->file('picture'));
        }

        return $logistic;
    }
}
