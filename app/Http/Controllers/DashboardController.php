<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        return response()->json([
            'message' => 'Dashboard data',
        ]);
    }
}
