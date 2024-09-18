<?php

namespace App\Repositories\PIC;

use App\Models\PIC;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentPIC extends EloquentRepository implements BaseRepository, PICRepository
{
    protected $model;

    public function __construct(PIC $pic)
    {
        $this->model = $pic;
    }

    public function all()
    {
        return PIC::all();
    }

    public function find($id)
    {
        return PIC::find($id);
    }

    public function store(Request $request)
    {
        $PIC = parent::store($request);
        if ($request->hasFile('picture')) {
            $PIC->saveImage($request->file('picture'));
        }

        return $PIC;
    }
}
