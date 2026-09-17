<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FuelBill;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use App\Models\Transaction;
use App\Models\Program; // <-- ADDED THIS IMPORT
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrialBalanceService
{
    /**
     * Main method to generate the full Trial Balance
     */
    public function generateTrialBalanceData($startDate, $endDate)
    {
        $headOrder = ['Assets', 'Liabilities', 'Equity', 'Income', 'Expenses'];
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        // 1. Get Chart of Account Balances (Assets, Liabilities, Equity, Income, Expenses)
        $coaBalances = $this->getChartOfAccountBalances($startDate, $endDate);
        foreach ($coaBalances as $head => $subHeads) {
            if (!isset($trialBalanceData[$head])) {
                $trialBalanceData[$head] = [];
            }
            
            foreach ($subHeads as $subHead => $data) {
                $trialBalanceData[$head][$subHead] = $data;
                $totalDebit += $data['subtotal_debit'];
                $totalCredit += $data['subtotal_credit'];
            }
        }

        // 2. Get Cash Account Balances (Using CashSheetBalanceService)
        $cashData = $this->getCashAccountBalances($endDate);
        if (!empty($cashData['accounts'])) {
            if (!isset($trialBalanceData['Assets'])) {
                $trialBalanceData['Assets'] = [];
            }
            $trialBalanceData['Assets']['Cash Accounts'] = $cashData;
            $totalDebit += $cashData['subtotal_debit'];
            $totalCredit += $cashData['subtotal_credit'];
        }

        // 3. Get Vendor Balances
        $vendorData = $this->getVendorBalances($startDate, $endDate);
        if (!empty($vendorData['accounts'])) {
            if (!isset($trialBalanceData['Assets'])) {
                $trialBalanceData['Assets'] = [];
            }
            $trialBalanceData['Assets']['Vendor Balances'] = $vendorData;
            $totalDebit += $vendorData['subtotal_debit'];
            $totalCredit += $vendorData['subtotal_credit'];
        }

        // 4. Get Program Transportation Cost (Carrying Bill)
        $transportationCost = Program::getGrandTotalCarryingBill($startDate);
        
        if ($transportationCost > 0) {
            if (!isset($trialBalanceData['Expenses'])) {
                $trialBalanceData['Expenses'] = [];
            }
            
            // Add as a new Sub Account Head so it appears after Operating Expense
            $trialBalanceData['Expenses']['Transportation Cost'] = [
                'accounts' => [
                    [
                        'id'           => 'trans_cost_custom',
                        'serial'       => '-',
                        'account_name' => 'Transportation Cost',
                        'debit'        => $transportationCost,
                        'credit'       => 0,
                        'link_type'    => null,
                        'link_id'      => null,
                    ]
                ],
                'subtotal_debit'  => $transportationCost,
                'subtotal_credit' => 0,
            ];
            
            $totalDebit += $transportationCost;
        }

        // 5. Reorder data according to standard accounting heads
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

        return [
            'orderedData' => $orderedData,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'difference' => $difference,
        ];
    }

    /**
     * Private Method: Handle Chart of Accounts
     */
    private function getChartOfAccountBalances($startDate, $endDate)
    {
        $accountStructure = ChartOfAccount::select('account_head', 'sub_account_head')
            ->distinct()
            ->orderBy('account_head')
            ->orderBy('sub_account_head')
            ->get();

        $data = [];

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

                $isPetrolPump = $account->petrol_pumps_id ? true : false;

                if ($transactions->isEmpty() && !$isPetrolPump) continue;

                $debit = 0;
                $credit = 0;

                switch ($structure->account_head) {
                    case 'Assets':
                        if ($structure->sub_account_head === 'Fixed Asset') {
                            $debit = $transactions->whereIn('tran_type', ['Purchase', 'Payment'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                            $credit = $transactions->whereIn('tran_type', ['Sold', 'Deprication'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        } else {
                            $debit = $transactions->whereIn('tran_type', ['Payment', 'Purchase'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                            $credit = $transactions->whereIn('tran_type', ['Received', 'Sold'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        }
                        break;

                    case 'Expenses':
                        $debit = $transactions->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust', 'Prepaid Adjust'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;

                    case 'Income':
                        $debit = $transactions->whereIn('tran_type', ['Refund'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = $transactions->whereIn('tran_type', ['Current', 'Advance Adjust', 'Receivable'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;

                    case 'Liabilities':
                        if ($isPetrolPump) {
                            $fuelBills = FuelBill::where('petrol_pump_id', $account->petrol_pumps_id)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->with(['programDetails.advancePayment'])
                                ->get();
                                
                            $credit = $fuelBills->sum(function ($bill) {
                                return $bill->total_fuel_amount;
                            });

                            $pumpTransactions = Transaction::where('petrol_pump_id', $account->petrol_pumps_id)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->get();
                                
                            $debit = $pumpTransactions->sum(fn($t) => $t->at_amount ?? 0);
                        } else {
                            $debit = $transactions->whereIn('tran_type', ['Payment'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                            $credit = $transactions->whereIn('tran_type', ['Received', 'Advance'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        }
                        break;

                    case 'Equity':
                        $debit = $transactions->whereIn('tran_type', ['Payment'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        $credit = $transactions->whereIn('tran_type', ['Received'])->sum(fn($t) => $t->at_amount ?? $t->amount ?? 0);
                        break;
                }

                $netBalance = $debit - $credit;

                if (abs($netBalance) > 0.009) {
                    $isNaturalDebit = in_array($structure->account_head, ['Assets', 'Expenses']);

                    if ($isNaturalDebit) {
                        $displayDebit  = $netBalance; 
                        $displayCredit = 0;
                    } else {
                        $displayDebit  = 0;
                        $displayCredit = -$netBalance; 
                    }

                    $accountList[] = [
                        'id'           => $account->id,
                        'serial'       => $account->serial,
                        'account_name' => $account->account_name,
                        'debit'        => $displayDebit,
                        'credit'       => $displayCredit,
                        'link_type'    => $structure->account_head, 
                        'link_id'      => $account->id,            
                    ];

                    $sectionDebit  += $displayDebit;
                    $sectionCredit += $displayCredit;
                }
            }

            if (!empty($accountList)) {
                if (!isset($data[$structure->account_head])) {
                    $data[$structure->account_head] = [];
                }
                $data[$structure->account_head][$structure->sub_account_head] = [
                    'accounts'        => $accountList,
                    'subtotal_debit'  => $sectionDebit,
                    'subtotal_credit' => $sectionCredit,
                ];
            }
        }

        return $data;
    }

    /**
     * Private Method: Handle Cash Accounts
     */
    private function getCashAccountBalances($endDate)
    {
        $cashService = app(CashSheetBalanceService::class);
        $balances = $cashService->getBalances($endDate);

        $cashAccounts = [
            ['id' => 1, 'name' => 'Office Cash', 'balance' => $balances['cashInHandClosing']],
            ['id' => 2, 'name' => 'Field Cash', 'balance' => $balances['cashInFieldClosing']],
        ];

        $cashAccountList = [];
        $cashSectionDebit = 0;
        $cashSectionCredit = 0;

        foreach ($cashAccounts as $cashAccount) {
            $netBalance = $cashAccount['balance'];

            if (abs($netBalance) > 0.009) {
                $displayDebit  = $netBalance > 0 ? $netBalance : 0;
                $displayCredit = $netBalance < 0 ? abs($netBalance) : 0;

                $cashAccountList[] = [
                    'id'           => 'cash_' . $cashAccount['id'],
                    'serial'       => '-',
                    'account_name' => $cashAccount['name'],
                    'debit'        => $displayDebit,
                    'credit'       => $displayCredit,
                ];

                $cashSectionDebit  += $displayDebit;
                $cashSectionCredit += $displayCredit;
            }
        }

        return [
            'accounts'        => $cashAccountList,
            'subtotal_debit'  => $cashSectionDebit,
            'subtotal_credit' => $cashSectionCredit,
        ];
    }

    /**
     * Private Method: Handle Vendor Balances
     */
    private function getVendorBalances($startDate, $endDate)
    {
        $vendors = Vendor::whereNull('deleted_at')->get();
        $vendorAccountList = [];
        $vendorSectionDebit = 0;
        $vendorSectionCredit = 0;

        $ledgerStartDate = Carbon::parse($startDate); 

        foreach ($vendors as $vendor) {
            $vendorBalance = $vendor->opening_balance;

            $vsequences = VendorSequenceNumber::with([
                'programDetail' => function ($query) {
                    $query->select(
                        'mother_vassel_id',
                        'vendor_sequence_number_id',
                        DB::raw('COUNT(DISTINCT challan_no) as total_trip'),
                        DB::raw('SUM(carrying_bill) as total_carrying_bill'),
                        DB::raw('SUM(dest_qty) as total_qty'),
                        DB::raw('SUM(scale_fee) as total_scale_fee'),
                        DB::raw('SUM(COALESCE(advance_payments.fuelamount,0) + COALESCE(advance_payments.cashamount,0)) as total_advance')
                    )
                    ->leftJoin('advance_payments', 'program_details.id', '=', 'advance_payments.program_detail_id')
                    ->with('motherVassel:id,name')
                    ->groupBy('mother_vassel_id', 'vendor_sequence_number_id');
                },
                'programDetail.advancePayment' => function ($query) {
                    $query->select(
                        'program_detail_id',
                        'fuelqty',
                        'fuelamount',
                        'cashamount'
                    );
                },
                'transaction' => function ($query) use ($endDate) {
                    $query->select(
                        'id',
                        'vendor_sequence_number_id',
                        'program_detail_id',
                        'chart_of_account_id',
                        'date',
                        'table_type',
                        'tran_type',
                        'payment_type',
                        'at_amount',
                        'account_id',
                        'note',
                        'description'
                    )
                    ->whereDate('date', '<=', $endDate); 
                }
            ])
            ->where('created_at', '>', $ledgerStartDate)
            ->whereDate('created_at', '<=', $endDate) 
            ->where('vendor_id', $vendor->id)
            ->orderBy('id', 'ASC')
            ->get();

            foreach ($vsequences as $sequence) {
                foreach ($sequence->programDetail as $detail) {
                    $netAmount = ($detail->total_carrying_bill + $detail->total_scale_fee) - $detail->total_advance;
                    $vendorBalance += $netAmount;
                }

                foreach ($sequence->transaction as $transaction) {
                    if ($transaction->table_type == "Income") {
                        $vendorBalance += $transaction->at_amount;
                    } else {
                        $vendorBalance -= $transaction->at_amount;
                    }
                }
            }

            if (abs($vendorBalance) > 0.009) {
                $displayDebit  = $vendorBalance;
                $displayCredit = 0;

                $vendorAccountList[] = [
                    'id'           => 'vendor_' . $vendor->id,
                    'serial'       => '-',
                    'account_name' => $vendor->name,
                    'debit'        => $displayDebit,
                    'credit'       => $displayCredit,
                    'link_type'    => 'Vendor',
                    'link_id'      => $vendor->id,
                ];

                $vendorSectionDebit  += $displayDebit;
                $vendorSectionCredit += $displayCredit;
            }
        }

        return [
            'accounts'        => $vendorAccountList,
            'subtotal_debit'  => $vendorSectionDebit,
            'subtotal_credit' => $vendorSectionCredit,
        ];
    }
}