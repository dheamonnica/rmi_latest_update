<?php

namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentDepartment extends EloquentRepository implements BaseRepository, DepartmentRepository
{
    protected $model;

    public function __construct(Department $department)
    {
        $this->model = $department;
    }

    public function all()
    {
        return Department::all();
    }

    public function find($id)
    {
        return Department::find($id);
    }

    public function store(Request $request)
    {
        $department = parent::store($request);
        if ($request->hasFile('picture')) {
            $department->saveImage($request->file('picture'));
        }

        return $department;
    }
}
