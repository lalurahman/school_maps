<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        $schools = School::where('school_type', 'NEGERI')
            ->whereIn('education_type', ['SMA', 'SMK'])
            ->get();
        // dd($schools); // Debugging line to check the data
        return view('welcome', compact('schools'));
    }

    public function sd()
    {
        $schools = DB::table('sekolah')->whereIn('bentuk_pendidikan', ['SD', 'MI'])
            // ->where('status_sekolah', 'NEGERI')
            ->get();
        // dd($schools); // Debugging line to check the data
        return view('welcome2', compact('schools'));
    }
}
