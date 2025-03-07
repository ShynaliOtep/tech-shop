<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function selectCity(Request $request)
    {
        $request->session()->put('selected_city', $request->city_id);
        return back();
    }
}
