<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function index()
    {
        // return view('frontend.index');
        if (Auth::user()) {
            return view('admin.dashboard');
        } else {
            return view('auth.login');
        }
    }
}
