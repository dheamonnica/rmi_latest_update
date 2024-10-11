<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentLoan extends EloquentRepository implements BaseRepository, LoanRepository
{
    protected $model;

    public function __construct(Loan $loan)
    {
        $this->model = $loan;
    }

    public function all()
    {
        return Loan::all();
    }

    public function find($id)
    {
        return Loan::find($id);
    }

    public function store(Request $request)
    {
        $loan = parent::store($request);
        if ($request->hasFile('picture')) {
            $loan->saveImage($request->file('picture'));
        }

        return $loan;
    }
    public function updateStatusApprove(Request $request, $loan)
    {
        $loan->approved_at = date("Y-m-d G:i:s");
        $loan->approved_by = Auth::user()->id;
        $loan->updated_at = date("Y-m-d G:i:s");
        $loan->updated_by = Auth::user()->id;
        $loan->status = 1;

        return $loan->save();
    }
}
