<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;

class CashSheetBalanceService
{
    private const START_DATE = '2025-07-20';
    private const CASH_IN_HAND_INITIAL = 347224.00;
    private const CASH_IN_FIELD_INITIAL = 321130.00;
    private const OFFICE_CASH_ACCOUNT_ID = 1;
    private const FIELD_CASH_ACCOUNT_ID = 2;

    /**
     * Get all 4 balances for any given date
     *
     * @param string|null $date
     * @param bool $sync  Pass true to auto-update accounts table
     */
    public function getBalances($date = null, bool $sync = false)
    {
        $date = $date
            ? Carbon::parse($date)->toDateString()
            : now()->subDay()->toDateString();

        $expectedDate = Carbon::parse($date)->subDay()->toDateString();

        if (Carbon::parse($expectedDate)->lt(Carbon::parse(self::START_DATE))) {
            $expectedDate = self::START_DATE;
        }

        // Opening balances
        if ($date == self::START_DATE) {
            $cashInHandOpening = self::CASH_IN_HAND_INITIAL;
            $cashInFieldOpening = self::CASH_IN_FIELD_INITIAL;
        } else {
            $previous = $this->calculateClosing(self::START_DATE, $expectedDate);
            $cashInHandOpening = $previous['office'];
            $cashInFieldOpening = $previous['field'];
        }

        // Closing balances
        $closing = $this->calculateClosing($date, $date, $cashInHandOpening, $cashInFieldOpening);

        $result = [
            'date'               => $date,
            'cashInHandOpening'  => round($cashInHandOpening, 2),
            'cashInFieldOpening' => round($cashInFieldOpening, 2),
            'cashInHandClosing'  => round($closing['office'], 2),
            'cashInFieldClosing' => round($closing['field'], 2),
        ];

        // Auto-sync if requested
        if ($sync) {
            $this->syncToAccounts($result);
        }

        return $result;
    }

    /**
     * Update accounts table with closing balances
     *
     * @param array|null $balances  Pass null to auto-calculate for yesterday
     * @return array The balances that were synced
     */
    public function syncToAccounts(?array $balances = null): array
    {
        // If no balances provided, calculate for yesterday
        if ($balances === null) {
            $balances = $this->getBalances();
        }

        Account::where('id', self::OFFICE_CASH_ACCOUNT_ID)->update([
            'amount' => $balances['cashInHandClosing'],
        ]);

        Account::where('id', self::FIELD_CASH_ACCOUNT_ID)->update([
            'amount' => $balances['cashInFieldClosing'],
        ]);

        return $balances;
    }

    /**
     * Core balance calculation
     */
    private function calculateClosing($startDate, $endDate, $openingOffice = null, $openingField = null)
    {
        $office = $openingOffice ?? self::CASH_IN_HAND_INITIAL;
        $field  = $openingField ?? self::CASH_IN_FIELD_INITIAL;

        $debitFilter = function ($q) {
            $q->where('tran_type', 'TransferIn')
              ->orWhere(fn($q2) => $q2->whereIn('table_type', ['Liabilities', 'Equity'])->where('tran_type', 'Received'))
              ->orWhere(fn($q2) => $q2->where('table_type', 'Income')->whereIn('tran_type', ['Current', 'Received', 'Wallet']));
        };

        $creditFilter = function ($q) {
            $q->where('tran_type', 'TransferOut')
              ->orWhere(fn($q2) => $q2->whereIn('table_type', ['Liabilities', 'Equity'])->where('tran_type', 'Payment'))
              ->orWhereIn('table_type', ['Expenses', 'Expense', 'Cogs'])
              ->orWhere(fn($q2) => $q2->where('table_type', 'Assets')->where('tran_type', 'Purchase'));
        };

        $debitOffice  = Transaction::where('account_id', 1)->whereBetween('date', [$startDate, $endDate])->where($debitFilter)->sum('amount');
        $debitField   = Transaction::where('account_id', 2)->whereBetween('date', [$startDate, $endDate])->where($debitFilter)->sum('amount');
        $creditOffice = Transaction::where('account_id', 1)->whereBetween('date', [$startDate, $endDate])->where($creditFilter)->sum('amount');
        $creditField  = Transaction::where('account_id', 2)->whereBetween('date', [$startDate, $endDate])->where($creditFilter)->sum('amount');

        $vendorAdvances = Transaction::where('tran_type', 'Advance')
            ->where('payment_type', 'Cash')
            ->whereHas('programDetail', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->sum('amount');

        return [
            'office' => $office + $debitOffice - $creditOffice,
            'field'  => $field + $debitField - $creditField - $vendorAdvances,
        ];
    }
}