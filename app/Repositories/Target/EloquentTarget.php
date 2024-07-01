<?php

namespace App\Repositories\Target;

use App\Models\Target;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentTarget extends EloquentRepository implements BaseRepository, TargetRepository
{
    protected $model;

    public function __construct(Target $target)
    {
        $this->model = $target;
    }

    public function all()
    {
        return Target::all();
    }

    public function find($id)
    {
        return Target::find($id);
    }

    public function store(Request $request)
    {
        $budget = parent::store($request);
        if ($request->hasFile('picture')) {
            $budget->saveImage($request->file('picture'));
        }

        return $budget;
    }
}
