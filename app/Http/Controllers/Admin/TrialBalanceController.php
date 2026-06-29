<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function trialBalance2(Request $request)
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

        // $allTranTypes = Transaction::whereBetween('date', [$startDate, $endDate])
        //     ->distinct()
        //     ->pluck('tran_type');
        // dd($allTranTypes);

        // $missedTransactions = Transaction::whereBetween('date', [$startDate, $endDate])
        //     ->whereIn('tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut', 'Prepaid Adjust'])
        //     ->selectRaw('tran_type, COUNT(*) as count, SUM(amount) as total')
        //     ->groupBy('tran_type')
        //     ->get();

        // dd($missedTransactions);

        // $missedWithHeads = Transaction::whereBetween('transactions.date', [$startDate, $endDate])
        //     ->whereIn('transactions.tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut', 'Prepaid Adjust'])
        //     ->join('chart_of_accounts', 'transactions.chart_of_account_id', '=', 'chart_of_accounts.id')
        //     ->selectRaw('chart_of_accounts.account_head, chart_of_accounts.sub_account_head, transactions.tran_type, COUNT(*) as count, SUM(transactions.amount) as total')
        //     ->groupBy('chart_of_accounts.account_head', 'chart_of_accounts.sub_account_head', 'transactions.tran_type')
        //     ->get();
        // dd($missedWithHeads);

        // $nullAccounts = Transaction::whereBetween('transactions.date', [$startDate, $endDate])
        //     ->whereIn('tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut'])
        //     ->selectRaw('tran_type, COUNT(*) as count, 
        //                 SUM(CASE WHEN chart_of_account_id IS NULL THEN 1 ELSE 0 END) as null_count,
        //                 SUM(CASE WHEN chart_of_account_id IS NOT NULL THEN 1 ELSE 0 END) as has_account')
        //     ->groupBy('tran_type')
        //     ->get();
        // dd($nullAccounts);

        // $orphaned = Transaction::whereBetween('transactions.date', [$startDate, $endDate])
        //     ->whereIn('tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut'])
        //     ->whereNotNull('chart_of_account_id')
        //     ->whereNotIn('chart_of_account_id', \App\Models\ChartOfAccount::pluck('id'))
        //     ->selectRaw('tran_type, COUNT(*) as count, SUM(amount) as total')
        //     ->groupBy('tran_type')
        //     ->get();
        // dd($orphaned);

        // $sample = Transaction::whereIn('tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut'])
        //     ->first();
        // dd($sample); // Look at ALL attributes to understand the data structure

        // $sample = Transaction::whereIn('tran_type', ['Advance', 'Wallet', 'TransferIn', 'TransferOut'])
        //     ->selectRaw('tran_type, 
        //         SUM(CASE WHEN vendor_id IS NOT NULL THEN 1 ELSE 0 END) as has_vendor,
        //         SUM(CASE WHEN equity_id IS NOT NULL THEN 1 ELSE 0 END) as has_equity,
        //         SUM(CASE WHEN expense_id IS NOT NULL THEN 1 ELSE 0 END) as has_expense,
        //         SUM(CASE WHEN income_id IS NOT NULL THEN 1 ELSE 0 END) as has_income,
        //         SUM(CASE WHEN liability_id IS NOT NULL THEN 1 ELSE 0 END) as has_liability,
        //         SUM(CASE WHEN liablity_id IS NOT NULL THEN 1 ELSE 0 END) as has_liablity,
        //         SUM(CASE WHEN asset_id IS NOT NULL THEN 1 ELSE 0 END) as has_asset,
        //         SUM(CASE WHEN account_id IS NOT NULL THEN 1 ELSE 0 END) as has_account
        //     ')
        //     ->groupBy('tran_type')
        //     ->get();
        // dd($sample);


        // 1. What does the vendors table look like?
        // $vendor = DB::table('vendors')->first();
        // dd($vendor);

        // 2. What does the accounts table look like?
        // $account = DB::table('accounts')->get();
        // dd($account);


        // 3. Sample an Advance transaction with its vendor
        // $advance = Transaction::where('tran_type', 'Advance')
        //     ->whereNotNull('vendor_id')
        //     ->with('vendor') // if relationship exists
        //     ->first();
        // dd($advance);

        // 4. Sample a TransferIn transaction with its account
        $transfer = Transaction::where('tran_type', 'TransferIn')
            ->whereNotNull('account_id')
            ->first();

        // Also get the linked account record
        $linkedAccount = DB::table('accounts')->where('id', $transfer->account_id)->first();
        dd(['transaction' => $transfer, 'account' => $linkedAccount]);

























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


    public function trialBalance(Request $request)
    {
        $startDate = '2025-07-20';
        $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();

        $headOrder = ['Assets', 'Liabilities', 'Equity', 'Income', 'Expenses'];
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        // ============================================================
        // PART 1: Original chart_of_accounts based transactions
        // ============================================================
        $accountStructure = ChartOfAccount::select('account_head', 'sub_account_head')
            ->distinct()
            ->orderBy('account_head')
            ->orderBy('sub_account_head')
            ->get();

        foreach ($accountStructure as $structure) {
            $accounts = ChartOfAccount::where('account_head', $structure->account_head)
                ->where('sub_account_head', $structure->sub_account_head)
                ->orderBy('serial')
                ->get();

            $accountList = [];
            $sectionDebit = 0;
            $sectionCredit = 0;

            foreach ($accounts as $account) {
                $transactions = Transaction::where('chart_of_account_id', $account->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                if ($transactions->isEmpty()) continue;

                $debit = 0;
                $credit = 0;

                switch ($structure->account_head) {
                    case 'Assets':
                        if ($structure->sub_account_head === 'Fixed Asset') {
                            $debit = $transactions->whereIn('tran_type', ['Purchase', 'Payment'])
                                ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                            $credit = $transactions->whereIn('tran_type', ['Sold', 'Deprication'])
                                ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        } else {
                            $debit = $transactions->whereIn('tran_type', ['Received', 'Purchase'])
                                ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                            $credit = $transactions->whereIn('tran_type', ['Payment', 'Sold'])
                                ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        }
                        break;

                    case 'Expenses':
                        $debit = $transactions->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust', 'Prepaid Adjust'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = 0;
                        break;

                    case 'Income':
                        $debit = $transactions->whereIn('tran_type', ['Refund'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = $transactions->whereIn('tran_type', ['Current', 'Advance Adjust', 'Receivable'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;

                    case 'Liabilities':
                        $debit = $transactions->whereIn('tran_type', ['Received'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = $transactions->whereIn('tran_type', ['Payment', 'Advance'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;

                    case 'Equity':
                        $debit = $transactions->whereIn('tran_type', ['Received'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = $transactions->whereIn('tran_type', ['Payment'])
                            ->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;
                }

                $netBalance = $debit - $credit;

                if (abs($netBalance) > 0.009) {
                    $displayDebit  = $netBalance > 0 ? $netBalance : 0;
                    $displayCredit = $netBalance < 0 ? abs($netBalance) : 0;

                    $accountList[] = [
                        'id'           => $account->id,
                        'serial'       => $account->serial,
                        'account_name' => $account->account_name,
                        'debit'        => $displayDebit,
                        'credit'       => $displayCredit,
                    ];

                    $sectionDebit  += $displayDebit;
                    $sectionCredit += $displayCredit;
                }
            }

            if (!empty($accountList)) {
                if (!isset($trialBalanceData[$structure->account_head])) {
                    $trialBalanceData[$structure->account_head] = [];
                }
                $trialBalanceData[$structure->account_head][$structure->sub_account_head] = [
                    'accounts'        => $accountList,
                    'subtotal_debit'  => $sectionDebit,
                    'subtotal_credit' => $sectionCredit,
                ];
                $totalDebit  += $sectionDebit;
                $totalCredit += $sectionCredit;
            }
        }

        // ============================================================
        // PART 2: Cash Accounts (Office Cash, Field Cash) 
        // via TransferIn / TransferOut / Wallet / Advance
        // These are ASSETS (Current Asset: Cash)
        // TransferIn = Cash received into account (Debit)
        // TransferOut = Cash paid out of account (Credit)
        // ============================================================
        $cashAccounts = DB::table('accounts')->whereNull('deleted_at')->get();
        $cashAccountList = [];
        $cashSectionDebit = 0;
        $cashSectionCredit = 0;

        foreach ($cashAccounts as $cashAccount) {
            $cashDebit = Transaction::where('account_id', $cashAccount->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('tran_type', ['TransferIn', 'Wallet'])
                ->sum('amount');

            $cashCredit = Transaction::where('account_id', $cashAccount->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('tran_type', ['TransferOut'])
                ->sum('amount');

            $netBalance = $cashDebit - $cashCredit;

            if (abs($netBalance) > 0.009) {
                $displayDebit  = $cashAccount->amount;
                $displayCredit = 0;

                $cashAccountList[] = [
                    'id'           => 'cash_' . $cashAccount->id,
                    'serial'       => '-',
                    'account_name' => $cashAccount->type,
                    'debit'        => $displayDebit,
                    'credit'       => $displayCredit,
                ];

                $cashSectionDebit  += $displayDebit;
                $cashSectionCredit += $displayCredit;
            }
        }

        if (!empty($cashAccountList)) {
            if (!isset($trialBalanceData['Assets'])) {
                $trialBalanceData['Assets'] = [];
            }
            $trialBalanceData['Assets']['Cash Accounts'] = [
                'accounts'        => $cashAccountList,
                'subtotal_debit'  => $cashSectionDebit,
                'subtotal_credit' => $cashSectionCredit,
            ];
            $totalDebit  += $cashSectionDebit;
            $totalCredit += $cashSectionCredit;
        }

        // ============================================================
        // PART 3: Vendor Advances
        // Advance paid to vendor = Asset (Debit increases, Credit decreases)
        // tran_type 'Advance' = cash given to vendor (Debit: Vendor Advance Asset)
        // When vendor delivers/settles = Credit side
        // ============================================================
        $vendors = DB::table('vendors')->whereNull('deleted_at')->get();
        $vendorAccountList = [];
        $vendorSectionDebit = 0;
        $vendorSectionCredit = 0;

        foreach ($vendors as $vendor) {
            $vendorDebit = Transaction::where('vendor_id', $vendor->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('tran_type', ['Advance', 'Wallet'])
                ->sum('amount');

            // Add credit side if you have vendor payment settlements
            $vendorCredit = Transaction::where('vendor_id', $vendor->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('tran_type', ['Received']) // adjust if vendors repay/settle differently
                ->sum('amount');

            $netBalance = $vendorDebit - $vendorCredit;

            if (abs($netBalance) > 0.009) {
                $displayDebit  = $netBalance > 0 ? $netBalance : 0;
                $displayCredit = $netBalance < 0 ? abs($netBalance) : 0;

                $vendorAccountList[] = [
                    'id'           => 'vendor_' . $vendor->id,
                    'serial'       => '-',
                    'account_name' => $vendor->name,
                    'debit'        => $displayDebit,
                    'credit'       => $displayCredit,
                ];

                $vendorSectionDebit  += $displayDebit;
                $vendorSectionCredit += $displayCredit;
            }
        }

        if (!empty($vendorAccountList)) {
            if (!isset($trialBalanceData['Assets'])) {
                $trialBalanceData['Assets'] = [];
            }
            $trialBalanceData['Assets']['Vendor Advances'] = [
                'accounts'        => $vendorAccountList,
                'subtotal_debit'  => $vendorSectionDebit,
                'subtotal_credit' => $vendorSectionCredit,
            ];
            $totalDebit  += $vendorSectionDebit;
            $totalCredit += $vendorSectionCredit;
        }

        // ============================================================
        // Reorder and return
        // ============================================================
        $orderedData = [];
        foreach ($headOrder as $head) {
            if (isset($trialBalanceData[$head])) {
                $orderedData[$head] = $trialBalanceData[$head];
            }
        }
        foreach ($trialBalanceData as $head => $subHeads) {
            if (!isset($orderedData[$head])) {
                $orderedData[$head] = $subHeads;
            }
        }

        $difference = $totalDebit - $totalCredit;

        return view('admin.accounts.trial_balance.index', compact(
            'orderedData', 'totalDebit', 'totalCredit', 'difference', 'startDate', 'endDate'
        ));
    }

    
}