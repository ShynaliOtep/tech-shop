<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function changeLang(Request $request, string $lang): RedirectResponse
    {
        session()->put('locale', $lang);

        return redirect()->back();
    }
}
