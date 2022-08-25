<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index() {
        return view('app.landing');
    }

    public function privacy() {
        return view('app.privacy_policy');
    }
}
