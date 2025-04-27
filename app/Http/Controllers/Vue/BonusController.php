<?php

namespace App\Http\Controllers\Vue;

use App\Http\Controllers\Controller;

class BonusController extends Controller
{
    public function index()
    {
        return view('vue.bonus');
    }
}
