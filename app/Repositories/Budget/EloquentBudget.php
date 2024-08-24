<?php

namespace App\Repositories\Budget;

use App\Models\Budget;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentBudget extends EloquentRepository implements BaseRepository, BudgetRepository
{
    protected $model;

    public function __construct(Budget $budget)
    {
        $this->model = $budget;
    }

    public function all()
    {
        return budget::all();
    }

    public function find($id)
    {
        return Budget::find($id);
    }

    public function store(Request $request)
    {
        $budget = parent::store($request);
        if ($request->hasFile('picture')) {
            $budget->saveImage($request->file('picture'));
        }

        return $budget;
    }

    public function updateStatusApprove(Request $request, $budget)
    {
        if (!$budget instanceof Budget) {
            $budget = $this->model->find($budget);
        }

        $budget->approved_at = date("Y-m-d G:i:s");
        $budget->approved_by = Auth::user()->id;
        $budget->updated_at = date("Y-m-d G:i:s");
        $budget->updated_by = Auth::user()->id;
        $budget->status = 1;

        return $budget->save();
    }
}
