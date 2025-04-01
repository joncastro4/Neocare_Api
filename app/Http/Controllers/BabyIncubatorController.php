<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BabyIncubator;
class BabyIncubatorController extends Controller
{
    public function index()
    {
        $babyIncubators = BabyIncubator::all();
        return response()->json([
            'babyIncubators' => $babyIncubators
        ], 200);
    }
}
