<?php

namespace App\Repositories\Absence;

use App\Models\Absence;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentAbsence extends EloquentRepository implements BaseRepository, AbsenceRepository
{
    protected $model;

    public function __construct(Absence $absence)
    {
        $this->model = $absence;
    }

    public function all()
    {
        return Absence::all();
    }

    public function find($id)
    {
        return Absence::find($id);
    }

    public function store(Request $request)
    {
        $absence = parent::store($request);
        if ($request->hasFile('picture')) {
            $absence->saveImage($request->file('picture'));
        }

        return $absence;
    }

    public static function checkIfUserHasClockIn()
    {
        $result = DB::table('absence')->where('user_id', Auth::user()->id)
        ->whereDate('clock_in', today())
        ->first();
        return $result;
    }

    public static function checkIfUserHasClockOut()
    {
        $result = DB::table('absence')->where('user_id', Auth::user()->id)
        ->whereDate('clock_out', today())
        ->first();
        return $result;
    }

    public static function clockOut()
    {
        $result = DB::table('absence')
        ->where('user_id', Auth::user()->id)
        ->whereDate('clock_in', today())
        ->whereNull('clock_out')
        ->update([
            'clock_out' => now()->format('Y-m-d H:i:s') // Update the 'clock_out' column with the current timestamp
        ]);
        return $result;
    }
}
