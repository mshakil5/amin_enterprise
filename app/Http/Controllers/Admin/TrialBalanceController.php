<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotherVassel;

class TrialBalanceController extends Controller
{
    public function trialBalance(Request $request)
    {
        $mvassels = MotherVassel::latest()->get();

        if ($request->isMethod('post')) {
          $motherVassel = MotherVassel::find($request->mv_id);
          return view('admin.accounts.trial_balance.index', compact('mvassels', 'motherVassel'));
        }

        return view('admin.accounts.trial_balance.index', compact('mvassels'));
    }
}
