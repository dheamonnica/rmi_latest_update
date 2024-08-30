<?php

namespace App\Repositories\Requirement;

use App\Models\Requirement;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentRequirement extends EloquentRepository implements BaseRepository, RequirementRepository
{
    protected $model;

    public function __construct(Requirement $requirement)
    {
        $this->model = $requirement;
    }

    public function all()
    {
        return Requirement::all();
    }

    public function find($id)
    {
        return Requirement::find($id);
    }

    public function store(Request $request)
    {
        $requirement = parent::store($request);
        if ($request->hasFile('picture')) {
            $requirement->saveImage($request->file('picture'));
        }

        return $requirement;
    }
}
