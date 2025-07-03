<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CashSheetController extends Controller
{
    public function cashSheet()
    {
      return view('admin.accounts.cash_sheet.index');
    }
}
