<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class IncomeStatementController extends Controller
{
    public function index()
    {
        // Default to current month if not specified
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->toDateString();

        return view('admin.accounts.profit-loss.index', compact('startDate', 'endDate'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Ensure start date is not before 2025-07-20
        if (\Carbon\Carbon::parse($startDate)->lt(\Carbon\Carbon::parse('2025-07-20'))) {
            $startDate = '2025-07-20';
        }

        // =============================================
        // 1. REVENUE (Income)
        // =============================================
        $incomeQuery = Transaction::with('chartOfAccount')
            ->where('table_type', 'Income')
            ->whereIn('tran_type', ['Current', 'Received', 'Wallet'])
            ->whereBetween('date', [$startDate, $endDate]);

        $incomeByAccount = (clone $incomeQuery)
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('chart_of_account_id')
            ->with('chartOfAccount')
            ->orderByDesc('total')
            ->get();

        $totalGrossIncome = (clone $incomeQuery)->sum('amount');

        // Income Refunds (deduct from revenue)
        $totalRefunds = Transaction::where('table_type', 'Income')
            ->where('tran_type', 'Refund')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $netRevenue = $totalGrossIncome - $totalRefunds;

        // =============================================
        // 2. COST OF GOODS SOLD (COGS)
        // =============================================
        $cogsQuery = Transaction::with('chartOfAccount')
            ->where('table_type', 'Cogs')
            ->whereIn('tran_type', ['Current', 'Prepaid Adjust', 'Due'])
            ->whereBetween('date', [$startDate, $endDate]);

        $cogsByAccount = (clone $cogsQuery)
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('chart_of_account_id')
            ->with('chartOfAccount')
            ->orderByDesc('total')
            ->get();

        $totalCogs = (clone $cogsQuery)->sum('amount');

        $grossProfit = $netRevenue - $totalCogs;

        // =============================================
        // 3. OPERATING EXPENSES
        // =============================================
        $expenseQuery = Transaction::with('chartOfAccount')
            ->where('table_type', 'Expenses')
            ->whereIn('tran_type', ['Current', 'Prepaid Adjust', 'Due'])
            ->whereBetween('date', [$startDate, $endDate]);

        $expensesByAccount = (clone $expenseQuery)
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('chart_of_account_id')
            ->with('chartOfAccount')
            ->orderByDesc('total')
            ->get();

        $totalOperatingExpenses = (clone $expenseQuery)->sum('amount');

        $netOperatingProfit = $grossProfit - $totalOperatingExpenses;

        // =============================================
        // 4. OTHER INCOME/EXPENSES (Non-Operating)
        // =============================================
        
        // Depreciation from Assets
        $depreciationByAccount = Transaction::with('chartOfAccount')
            ->where('table_type', 'Assets')
            ->where('tran_type', 'Depreciation')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('chart_of_account_id')
            ->with('chartOfAccount')
            ->orderByDesc('total')
            ->get();

        $totalDepreciation = Transaction::where('table_type', 'Assets')
            ->where('tran_type', 'Depreciation')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Asset Sales (Other Income)
        $assetSalesByAccount = Transaction::with('chartOfAccount')
            ->where('table_type', 'Assets')
            ->where('tran_type', 'Sold')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('chart_of_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('chart_of_account_id')
            ->with('chartOfAccount')
            ->orderByDesc('total')
            ->get();

        $totalAssetSales = Transaction::where('table_type', 'Assets')
            ->where('tran_type', 'Sold')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalOtherIncome = $totalAssetSales;
        $totalOtherExpenses = $totalDepreciation;
        $netOtherIncome = $totalOtherIncome - $totalOtherExpenses;

        // =============================================
        // 5. NET PROFIT
        // =============================================
        $netProfit = $netOperatingProfit + $netOtherIncome;

        // =============================================
        // 6. COMPARISON DATA (Previous Period)
        // =============================================
        $periodDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $prevStartDate = \Carbon\Carbon::parse($startDate)->subDays($periodDays)->toDateString();
        $prevEndDate = \Carbon\Carbon::parse($startDate)->subDay()->toDateString();

        // Ensure previous period doesn't go before 2025-07-20
        if (\Carbon\Carbon::parse($prevStartDate)->lt(\Carbon\Carbon::parse('2025-07-20'))) {
            $prevStartDate = '2025-07-20';
        }

        $prevNetProfit = $this->calculateNetProfit($prevStartDate, $prevEndDate);
        $profitChange = $prevNetProfit > 0 ? (($netProfit - $prevNetProfit) / $prevNetProfit) * 100 : 0;

        // =============================================
        // 7. SUMMARY STATS
        // =============================================
        $grossProfitMargin = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;
        $netProfitMargin = $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0;
        $operatingExpenseRatio = $netRevenue > 0 ? ($totalOperatingExpenses / $netRevenue) * 100 : 0;

        return view('admin.accounts.profit-loss.index', compact(
            'startDate',
            'endDate',
            'incomeByAccount',
            'totalGrossIncome',
            'totalRefunds',
            'netRevenue',
            'cogsByAccount',
            'totalCogs',
            'grossProfit',
            'expensesByAccount',
            'totalOperatingExpenses',
            'netOperatingProfit',
            'depreciationByAccount',
            'totalDepreciation',
            'assetSalesByAccount',
            'totalAssetSales',
            'totalOtherIncome',
            'totalOtherExpenses',
            'netOtherIncome',
            'netProfit',
            'prevNetProfit',
            'profitChange',
            'grossProfitMargin',
            'netProfitMargin',
            'operatingExpenseRatio',
            'periodDays'
        ));
    }

    private function calculateNetProfit($startDate, $endDate)
    {
        $income = Transaction::where('table_type', 'Income')
            ->whereIn('tran_type', ['Current', 'Received', 'Wallet'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $refunds = Transaction::where('table_type', 'Income')
            ->where('tran_type', 'Refund')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $cogs = Transaction::where('table_type', 'Cogs')
            ->whereIn('tran_type', ['Current', 'Prepaid Adjust', 'Due'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = Transaction::where('table_type', 'Expenses')
            ->whereIn('tran_type', ['Current', 'Prepaid Adjust', 'Due'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $depreciation = Transaction::where('table_type', 'Assets')
            ->where('tran_type', 'Depreciation')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $assetSales = Transaction::where('table_type', 'Assets')
            ->where('tran_type', 'Sold')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return ($income - $refunds) - $cogs - $expenses - $depreciation + $assetSales;
    }

    public function downloadPdf(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        // Generate the same data as generate()
        // ... (same logic as generate method)
        
        // For now, redirect to the view with print option
        return redirect()->route('admin.profit-loss.generate', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}