<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function links(): View
    {
        return view('links.index');
    }

    public function pix(): View
    {
        return view('pix.index');
    }
}
