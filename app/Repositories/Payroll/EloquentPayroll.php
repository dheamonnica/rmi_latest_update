<?php

namespace App\Repositories\Payroll;

use App\Models\Payroll;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentPayroll extends EloquentRepository implements BaseRepository, PayrollRepository
{
    protected $model;

    public function __construct(Payroll $payroll)
    {
        $this->model = $payroll;
    }

    public function all()
    {
        return Payroll::all();
    }

    public function find($id)
    {
        return Payroll::find($id);
    }

    public function store(Request $request)
    {
        $payroll = parent::store($request);
        if ($request->hasFile('picture')) {
            $payroll->saveImage($request->file('picture'));
        }

        return $payroll;
    }
}
