<?php

if (!function_exists('cash_balances')) {
    /**
     * Get all 4 cash balances for a date.
     * Returns array with: date, cashInHandOpening, cashInFieldOpening, cashInHandClosing, cashInFieldClosing
     */
    function cash_balances($date = null)
    {
        return app(\App\Services\CashSheetBalanceService::class)->getBalances($date);
    }
}