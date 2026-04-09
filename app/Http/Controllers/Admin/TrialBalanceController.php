<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function trialBalance(Request $request)
    {
        $startDate = '2025-07-20';
        $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();

        // Get all distinct account heads and sub account heads with ordering
        $accountStructure = ChartOfAccount::select('account_head', 'sub_account_head')
            ->distinct()
            ->orderBy('account_head')
            ->orderBy('sub_account_head')
            ->get();

        // Define the order of account heads for display
        $headOrder = ['Assets', 'Liabilities', 'Equity', 'Income', 'Expenses'];
        
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accountStructure as $structure) {
            $accounts = ChartOfAccount::where('account_head', $structure->account_head)
                ->where('sub_account_head', $structure->sub_account_head)
                ->orderBy('serial')
                ->get();

            $accountList = [];
            $sectionDebit = 0;
            $sectionCredit = 0;

            foreach ($accounts as $account) {
                // Get transactions for this account within date range
                $transactions = Transaction::where('chart_of_account_id', $account->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                if ($transactions->isEmpty()) {
                    continue;
                }

                $debit = 0;
                $credit = 0;

                switch ($structure->account_head) {
                    case 'Assets':
                        if ($structure->sub_account_head === 'Fixed Asset') {
                            // Fixed Asset: Purchase (Dr), Sold (Cr), Depreciation (Cr)
                            $debit = $transactions->whereIn('tran_type', ['Purchase', 'Payment'])
                                ->sum(function ($t) {
                                    return $t->at_amount ?? $t->amount ?? 0;
                                });
                            $credit = $transactions->whereIn('tran_type', ['Sold', 'Deprication'])
                                ->sum(function ($t) {
                                    return $t->at_amount ?? $t->amount ?? 0;
                                });
                        } else {
                            // Current Asset: Received (Dr), Payment (Cr)
                            $debit = $transactions->whereIn('tran_type', ['Received', 'Purchase'])
                                ->sum(function ($t) {
                                    return $t->at_amount ?? $t->amount ?? 0;
                                });
                            $credit = $transactions->whereIn('tran_type', ['Payment', 'Sold'])
                                ->sum(function ($t) {
                                    return $t->at_amount ?? $t->amount ?? 0;
                                });
                        }
                        break;

                    case 'Expenses':
                        // Expenses: Current, Prepaid, Due Adjust (Dr)
                        $debit = $transactions->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        $credit = 0;
                        break;

                    case 'Income':
                        // Income: Current, Advance Adjust, Receivable (Cr), Refund (Dr)
                        $debit = $transactions->whereIn('tran_type', ['Refund'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        $credit = $transactions->whereIn('tran_type', ['Current', 'Advance Adjust', 'Receivable'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        break;

                    case 'Liabilities':
                        // Liabilities: Payment (Cr - increases liability), Received (Dr - decreases liability)
                        $debit = $transactions->whereIn('tran_type', ['Received'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        $credit = $transactions->whereIn('tran_type', ['Payment'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        break;

                    case 'Equity':
                        // Equity: Received (Cr - increases equity), Payment (Dr - decreases equity)
                        $debit = $transactions->whereIn('tran_type', ['Payment'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        $credit = $transactions->whereIn('tran_type', ['Received'])
                            ->sum(function ($t) {
                                return $t->at_amount ?? $t->amount ?? 0;
                            });
                        break;
                }

                $netBalance = $debit - $credit;

                // Only show accounts with balance
                if (abs($netBalance) > 0.009) {
                    $displayDebit = $netBalance > 0 ? $netBalance : 0;
                    $displayCredit = $netBalance < 0 ? abs($netBalance) : 0;

                    $accountList[] = [
                        'id' => $account->id,
                        'serial' => $account->serial,
                        'account_name' => $account->account_name,
                        'debit' => $displayDebit,
                        'credit' => $displayCredit,
                    ];

                    $sectionDebit += $displayDebit;
                    $sectionCredit += $displayCredit;
                }
            }

            // Only add section if it has accounts with balance
            if (!empty($accountList)) {
                if (!isset($trialBalanceData[$structure->account_head])) {
                    $trialBalanceData[$structure->account_head] = [];
                }
                
                $trialBalanceData[$structure->account_head][$structure->sub_account_head] = [
                    'accounts' => $accountList,
                    'subtotal_debit' => $sectionDebit,
                    'subtotal_credit' => $sectionCredit,
                ];

                $totalDebit += $sectionDebit;
                $totalCredit += $sectionCredit;
            }
        }

        // Reorder trial balance data according to headOrder
        $orderedData = [];
        foreach ($headOrder as $head) {
            if (isset($trialBalanceData[$head])) {
                $orderedData[$head] = $trialBalanceData[$head];
            }
        }
        // Add any remaining heads not in the predefined order
        foreach ($trialBalanceData as $head => $subHeads) {
            if (!isset($orderedData[$head])) {
                $orderedData[$head] = $subHeads;
            }
        }

        // Calculate difference (should be 0 if balanced)
        $difference = $totalDebit - $totalCredit;

        return view('admin.accounts.trial_balance.index', compact(
            'orderedData',
            'totalDebit',
            'totalCredit',
            'difference',
            'startDate',
            'endDate'
        ));
    }

    
}