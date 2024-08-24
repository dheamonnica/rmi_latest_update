<?php

namespace App\Repositories\Segment;

use App\Models\Segment;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentSegment extends EloquentRepository implements BaseRepository, SegmentRepository
{
    protected $model;

    public function __construct(Segment $segment)
    {
        $this->model = $segment;
    }

    public function all()
    {
        return Segment::all();
    }

    public function find($id)
    {
        return Segment::find($id);
    }

    public function store(Request $request)
    {
        $segment = parent::store($request);
        if ($request->hasFile('picture')) {
            $segment->saveImage($request->file('picture'));
        }

        return $segment;
    }
}
