<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $schools = School::where('school_type', 'NEGERI')
            // ->where('education_type', 'SMA')
            ->get();
        // dd($schools); // Debugging line to check the data
        return view('welcome', compact('schools'));
    }
}
