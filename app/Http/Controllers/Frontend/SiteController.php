<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Routing\Controller as BaseController;

class SiteController extends BaseController
{
    public function index()
    {
        return view('frontend.index');
    }
}
