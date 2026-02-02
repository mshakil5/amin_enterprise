<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\MotherVasselController;
use App\Http\Controllers\Admin\LighterVasselController;
use App\Http\Controllers\Admin\GhatController;
use App\Http\Controllers\Admin\PumpController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientRateController;
use App\Http\Controllers\Admin\DaybookController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\GeneratingBillController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\LiabilityController;
use App\Http\Controllers\Admin\EquityController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ChartOfAccountController;
use App\Http\Controllers\Admin\PettyCashController;
use App\Http\Controllers\Admin\PLStatementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TrialBalanceController;
use App\Http\Controllers\Admin\IncomeStatementController;
use App\Http\Controllers\Admin\FinancialStatementController;
use App\Http\Controllers\Admin\CashSheetController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\VendorLedgerController;
use App\Http\Controllers\Admin\ExcelUploadController;

/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    //profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('profile/{id}', [AdminController::class, 'adminProfileUpdate']);
    Route::post('changepassword', [AdminController::class, 'changeAdminPassword']);
    Route::put('image/{id}', [AdminController::class, 'adminImageUpload']);
    //profile end

    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);
    
    Route::get('/agent', [AgentController::class, 'index'])->name('admin.agent');
    Route::post('/agent', [AgentController::class, 'store']);
    Route::get('/agent/{id}/edit', [AgentController::class, 'edit']);
    Route::post('/agent-update', [AgentController::class, 'update']);
    Route::get('/agent/{id}', [AgentController::class, 'delete']);

    Route::get('/country', [CountryController::class, 'index'])->name('admin.country');
    Route::post('/country', [CountryController::class, 'store']);
    Route::get('/country/{id}/edit', [CountryController::class, 'edit']);
    Route::post('/country-update', [CountryController::class, 'update']);
    Route::get('/country/{id}', [CountryController::class, 'delete']);


    // petty cash
    Route::get('/petty-cash', [PettyCashController::class, 'index'])->name('admin.pettycash');
    Route::post('/petty-cash', [PettyCashController::class, 'store']);
    Route::get('/petty-cash/{id}/edit', [PettyCashController::class, 'edit']);
    Route::post('/petty-cash-update', [PettyCashController::class, 'update']);

    //Accounts
    Route::get('/account', [AccountController::class, 'index'])->name('admin.account');
    Route::post('/account', [AccountController::class, 'store']);
    Route::get('/account/{id}/edit', [AccountController::class, 'edit']);
    Route::post('/account-update', [AccountController::class, 'update']);
    Route::post('/account/transfer', [AccountController::class, 'transfer'])->name('admin.account.transfer');

    Route::get('/mother-vassel', [MotherVasselController::class, 'index'])->name('admin.mothervassel');
    Route::get('/get-consignment-number', [MotherVasselController::class, 'getConsignmentNumber'])->name('admin.getConsignmentNumber');
    Route::post('/mother-vassel', [MotherVasselController::class, 'store'])->name('admin.mothervassel.store');
    Route::get('/mother-vassel/{id}/edit', [MotherVasselController::class, 'edit']);
    Route::post('/mother-vassel-update', [MotherVasselController::class, 'update']);
    Route::get('/mother-vassel/{id}', [MotherVasselController::class, 'delete']);

    Route::post('/mother-vassel/status/{id}', [MotherVasselController::class, 'updateStatus']);


    Route::get('/lighter-vassel', [LighterVasselController::class, 'index'])->name('lightervassel');
    Route::post('/lighter-vassel', [LighterVasselController::class, 'store'])->name('admin.lightervassel.store');
    Route::get('/lighter-vassel/{id}/edit', [LighterVasselController::class, 'edit']);
    Route::post('/lighter-vassel-update', [LighterVasselController::class, 'update']);
    Route::get('/lighter-vassel/{id}', [LighterVasselController::class, 'delete']);

    Route::get('/ghat', [GhatController::class, 'index'])->name('admin.ghat');
    Route::post('/ghat', [GhatController::class, 'store']);
    Route::get('/ghat/{id}/edit', [GhatController::class, 'edit']);
    Route::post('/ghat-update', [GhatController::class, 'update']);
    Route::get('/ghat/{id}', [GhatController::class, 'delete']);

    Route::get('/pump', [PumpController::class, 'index'])->name('admin.pump');
    Route::post('/pump', [PumpController::class, 'store']);
    Route::get('/pump/{id}/edit', [PumpController::class, 'edit']);
    Route::post('/pump-update', [PumpController::class, 'update']);
    Route::get('/pump/{id}', [PumpController::class, 'delete']);
    Route::post('/add-fuel-bill-number', [PumpController::class, 'addFuelBillNumber'])->name('admin.addFuelBillNumber');
    Route::post('/get-petrol-pump-bill', [PumpController::class, 'getFuelBillNumber']);
    Route::get('/get-pump-sequence-list/{id}', [PumpController::class, 'getPumpWiseProgramList'])->name('admin.pump.sequence.show');
    Route::post('/pump/update', [PumpController::class, 'pumpUpdate'])->name('admin.pump.update');

    Route::post('/petrol-pump/submit', [PumpController::class, 'updateMarkQty'])->name('petrol.pump.mark.qty');
    
    Route::get('/vendor', [VendorController::class, 'index'])->name('admin.vendor');
    Route::get('/vendor-list', [VendorController::class, 'vendorlist'])->name('admin.getVendors');
    Route::get('/get-vendors-list/{id}', [VendorController::class, 'getVendorListByClientId'])->name('admin.getVendorListByClientId');
    Route::get('/get-vendors-sequence-list/{id}', [VendorController::class, 'getVendorWiseProgramList'])->name('admin.vendor.sequence.show');
    Route::post('/vendor', [VendorController::class, 'store']);
    Route::get('/vendor/{id}/edit', [VendorController::class, 'edit']);
    Route::post('/vendor-update', [VendorController::class, 'update']);
    Route::get('/vendor/{id}', [VendorController::class, 'delete']);
    Route::post('/add-vendor-sequence', [VendorController::class,'addSequenceNumber'])->name('addSequenceNumber');
    Route::post('/get-vendor-sequence', [VendorController::class,'getSequenceNumber']);
    Route::post('/vendor-sequence/update-qty', [VendorController::class, 'updateQty'])->name('admin.vendor.sequence.qty.update');
    Route::get('/vendor-sequence/{id}', [VendorController::class, 'sequencedelete']);
    Route::post('/add-vendor-wallet-balance', [VendorController::class,'addWalletBalance'])->name('addWalletBalance');
    Route::post('/reduce-vendor-wallet-balance', [VendorController::class,'reduceWalletBalance'])->name('reduceWalletBalance');
    Route::post('/update-vendor-wallet-balance', [VendorController::class,'updateWalletBalance']);

    Route::get('/get-wallet-transaction/{id}', [VendorController::class,'getWalletTransaction'])->name('getWalletTransaction');
    Route::post('/vendor-trip/export-excel', [VendorController::class, 'exportExcel'])->name('admin.vendor-trip.export-excel');
    Route::post('/check-duplicate-data', [VendorController::class,'checkDuplicateWrongData'])->name('checkDuplicateWrongData');
    Route::get('/get-vendors-sequence-ledger/{id}', [VendorController::class, 'getVendorWiseProgramLedger'])->name('admin.vendor.sequence.ledger');
    Route::get('/vendor/{id}/sequences', [VendorController::class, 'getSequences']);

    
    Route::get('/get-without-trip-fuel-bill-adjust/{id}', [VendorController::class, 'getWithoutTripFuelBillAdjust'])->name('admin.withouttrip.fuelbill');



    // approved and checked sequence 
    Route::post('/vendor-sequence-approved', [VendorController::class,'addSequenceNumberApproved']);
    Route::post('/vendor-sequence-checked', [VendorController::class,'addSequenceNumberChecked']);

    // approved and checked sequence 
    
    Route::get('/client', [ClientController::class, 'index'])->name('admin.client');
    Route::post('/client', [ClientController::class, 'store']);
    Route::get('/client/{id}/edit', [ClientController::class, 'edit']);
    Route::post('/client-update', [ClientController::class, 'update']);
    Route::get('/client/{id}', [ClientController::class, 'delete']);

    
    Route::get('/destination', [DestinationController::class, 'index'])->name('admin.destination');
    Route::post('/destination', [DestinationController::class, 'store']);
    Route::get('/destination/{id}/edit', [DestinationController::class, 'edit']);
    Route::post('/destination-update', [DestinationController::class, 'update']);
    Route::get('/destination/{id}', [DestinationController::class, 'delete']);


    Route::get('/slab-rate', [DestinationController::class, 'slabRateIndex'])->name('admin.slabrate');
    Route::post('/slab-rate', [DestinationController::class, 'slabRatestore']);
    Route::get('/slab-rate/{id}/edit', [DestinationController::class, 'slabRateedit']);
    Route::post('/slab-rate-update', [DestinationController::class, 'slabRateupdate']);
    Route::get('/slab-rate/{id}', [DestinationController::class, 'slabRatedelete']);

    // client slab rate
    Route::get('/client-rate', [ClientRateController::class, 'index'])->name('admin.clientrate');
    Route::post('/client-rate', [ClientRateController::class, 'store']);
    Route::get('/client-rate/{id}/edit', [ClientRateController::class, 'edit']);
    Route::post('/client-rate-update', [ClientRateController::class, 'update']);
    Route::get('/client-rate/{id}', [ClientRateController::class, 'delete']);



    // program
    Route::get('/program', [ProgramController::class, 'allPrograms'])->name('admin.allProgram');
    Route::get('/deleted-program-details/{id}', [ProgramController::class, 'deletedProgramDetail'])
    ->name('admin.deletedProgramDetail');

    Route::get('/program/{id}/{type?}', [ProgramController::class, 'programDetail'])->name('admin.programDetail');
    Route::get('/programdetails', [ProgramController::class, 'vendorWiseProgramDetails'])->name('admin.program.details');
    Route::get('/program-vendor/{id}', [ProgramController::class, 'programVendor'])->name('admin.programVendorList');
    Route::get('/program-edit/{id}', [ProgramController::class, 'programEdit'])->name('admin.programEdit');
    Route::get('/program-details-edit/{id}', [ProgramController::class, 'programDetailsEdit'])->name('admin.programDetailsEdit');
    Route::get('/add-program', [ProgramController::class, 'createProgram'])->name('admin.addProgram');
    Route::get('/after-challan-rcv', [ProgramController::class, 'afterPostProgram'])->name('admin.afterPostProgram');
    Route::post('/check-challan', [ProgramController::class, 'checkChallan'])->name('admin.checkChallan');
    Route::post('/check-slab-rate', [ProgramController::class, 'checkSlabRate'])->name('admin.checkSlabRate');
    Route::get('/program-delete/{id}', [ProgramController::class, 'prgmDelete']);
    Route::post('/add-program', [ProgramController::class, 'store'])->name('programStore');

    Route::post('/add-more-challan', [ProgramController::class, 'addMoreChallan'])->name('addMoreChallan');
    
    Route::post('/update-program', [ProgramController::class, 'programUpdate'])->name('programUpdate');
    Route::post('/get-vendor-advance-by-date', [ProgramController::class, 'getVendorAdvanceByDate'])->name('getAdvancePayments');
    Route::post('/get-truc-list-by-vendor', [ProgramController::class, 'getProgramDetailsByVendor'])->name('getProgramDetailsByVendor');
    Route::post('/change-quantity', [ProgramController::class, 'changeQuantity'])->name('changeQuantity');
    Route::post('/undo-change-quantity', [ProgramController::class, 'undoChangeQuantity'])->name('undoChangeQuantity');

    Route::post('program/update-single-row', [ProgramController::class, 'updateSingleRow'])->name('admin.program.update-single-row');

    // program after challan store
    Route::post('/program-after-challan-store', [ProgramController::class, 'afterPostProgramStore'])->name('after-challan-store');
    Route::post('/single-programdetail-update', [ProgramController::class, 'singleProgramdetailUpdate'])->name('single-programdetail-update');

    // change fuel rate from pump unique id
    Route::post('/change-program-fuel-rate', [ProgramController::class, 'changeProgramFuelRate'])->name('change-program-fuel-rate');

    // billing
    Route::get('/bill', [TransactionController::class,'getBill'])->name('admin.getBill');
    Route::post('/check-bill', [TransactionController::class, 'checkBill'])->name('admin.checkBill');
    Route::post('/bill-store', [TransactionController::class, 'billStore'])->name('admin.billStore');

    // vendor payment
    Route::post('/vendor-pay', [TransactionController::class,'vendorAdvancePay'])->name('vendorAdvancePay');
    Route::post('/vendor-transaction', [TransactionController::class,'vendorTran'])->name('vendorAdvanceTran');

    Route::post('/add-destination-slab-rate', [ProgramController::class,'addDestinationSlabRate'])->name('addDestinationSlabRate');
    Route::post('/get-destination-slab-rate', [ProgramController::class,'getDestinationSlabRate'])->name('getDestinationSlabRate');
    Route::post('/destination-slab-rate-update', [ProgramController::class,'updateDestinationSlabRate'])->name('updateDestinationSlabRate');


    // ledger
    Route::get('/vendor-ledger', [LedgerController::class, 'vendorLedger'])->name('vendorLedger');
    Route::post('/vendor-ledger', [LedgerController::class, 'vendorVasselLedger'])->name('vendorVasselLedger');
    Route::get('/ledger-receivable', [LedgerController::class, 'receivableLedger'])->name('receivableLedger');
    Route::post('/ledger-receivable', [LedgerController::class, 'receivableLedger'])->name('receivableLedger.Search');
    Route::get('/ledger-advance', [LedgerController::class, 'advanceLedger'])->name('advanceLedger');
    Route::post('/ledger-advance', [LedgerController::class, 'advanceLedger'])->name('advanceLedger.Search');
    Route::get('/ledger-payable', [LedgerController::class, 'payableLedger'])->name('payableLedger');
    Route::post('/ledger-payable', [LedgerController::class, 'payableLedger'])->name('payableLedger.Search');


    // before posting challan report
    Route::get('/before-posting-challan-report/{vid}/{mid}', [ReportController::class, 'challanPostingReport'])->name('challanPostingReport');
    Route::get('/before-posting-challan-report', [ReportController::class, 'challanPostingVendorReport'])->name('challanPostingVendorReport');
    Route::post('/before-posting-challan-report', [ReportController::class, 'challanPostingVendorReport'])->name('challanPostingVendorReportshow');
    Route::post('report-notes', [ReportController::class,'storeReportNotes'])->name('reportNotes.store');
    Route::put('report-notes/{note}', [ReportController::class,'updateNote'])->name('reportNotes.update');

    Route::delete('/program-details/{id}', [ReportController::class, 'deleteProgramDetails'])->name('programDetails.delete');

    Route::post('/due-payment/store', [ReportController::class, 'storeDuePayment'])->name('due.payment.store');
    
    Route::get('/challan-posting-date-report/{id}', [ReportController::class, 'challanPostingDateReport'])->name('challanPostingDateReport');
    


    // bill generating
    Route::get('/bill-generating/{id}', [GeneratingBillController::class, 'billGenerating'])->name('billGenerating');
    Route::get('/bill-generated/{id}', [GeneratingBillController::class, 'billGenerated'])->name('bill.generated');
    Route::get('/bill-not-generated/{id}', [GeneratingBillController::class, 'billNotGenerated'])->name('bill.not.generated');
    Route::get('/generating-bill-show/{id}', [GeneratingBillController::class, 'billGeneratingShow'])->name('generatingBillShow');
    Route::post('/bill-generating', [GeneratingBillController::class, 'billGeneratingStore'])->name('billGeneratingStore');
    Route::post('/update-oldqty', [GeneratingBillController::class, 'updateOldQty'])->name('updateOldQty');
    Route::get('/export-template', [GeneratingBillController::class, 'exportTemplate'])->name('export.template');
    Route::get('/export-program-details/{id}', [GeneratingBillController::class, 'exportProgramDetails'])->name('export.programDetails');
    Route::post('/generate-bill', [GeneratingBillController::class, 'generateBill'])->name('bill.generate');
    Route::patch('program-detail/{id}/undo', [GeneratingBillController::class, 'undoGenerateBill'])->name('generateBill.undo');
    Route::patch('fuel-bill/undo/{id}', [ProgramController::class, 'undoFuelBill'])->name('fuel.bill.undo');

    
    // daybook
    Route::get('cash-book', [DaybookController::class, 'cashbook'])->name('admin.cashbook');
    Route::get('bank-book', [DaybookController::class, 'bankbook'])->name('admin.bankbook');
    Route::get('day-book', [DaybookController::class, 'daybook'])->name('admin.daybook');


    //Chart of account
    Route::get('chart-of-account', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount');
    Route::post('chart-of-accounts', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount.filter');
    Route::post('chart-of-account', [ChartOfAccountController::class, 'store']);
    Route::get('chart-of-account/{id}', [ChartOfAccountController::class, 'edit']);
    Route::put('chart-of-account/{id}', [ChartOfAccountController::class, 'update']);
    Route::get('chart-of-account/{id}/change-status', [ChartOfAccountController::class, 'changeStatus']);

    Route::get('transactions/{id}/reverse', [ChartOfAccountController::class, 'reverse'])
        ->name('admin.transactions.reverse');

    Route::post('transactions/reverse-save', [ChartOfAccountController::class, 'reverseSave'])
        ->name('admin.transactions.reverse.save');

    //Income
    Route::get('income', [IncomeController::class, 'index'])->name('admin.income');
    Route::post('incomes', [IncomeController::class, 'index'])->name('admin.income.filter');
    Route::post('income', [IncomeController::class, 'store']);
    Route::get('income/{id}', [IncomeController::class, 'edit']);
    Route::put('income/{id}', [IncomeController::class, 'update']); 

    //Liability
    Route::get('liabilities', [LiabilityController::class, 'index'])->name('admin.liabilities');
    Route::post('liability', [LiabilityController::class, 'index'])->name('admin.liability.filter');
    Route::post('liabilities', [LiabilityController::class, 'store']);
    Route::get('liabilities/{id}', [LiabilityController::class, 'edit']);
    Route::put('liabilities/{id}', [LiabilityController::class, 'update']);

    //Equity
    Route::get('equity', [EquityController::class, 'index'])->name('admin.equity');
    Route::post('equities', [EquityController::class, 'index'])->name('admin.equity.filter');
    Route::post('equity', [EquityController::class, 'store']);
    Route::get('equity/{id}', [EquityController::class, 'edit']);
    Route::put('equity/{id}', [EquityController::class, 'update']);
    
    //Asset
    Route::get('asset', [AssetController::class, 'index'])->name('admin.asset');
    Route::post('assets', [AssetController::class, 'index'])->name('admin.asset.filter');
    Route::post('asset', [AssetController::class, 'store']);
    Route::get('asset/{id}', [AssetController::class, 'edit']);
    Route::put('asset/{id}', [AssetController::class, 'update']); 

    //Expense
    Route::get('expense', [ExpenseController::class, 'index'])->name('admin.expense');
    Route::post('expenses', [ExpenseController::class, 'index'])->name('admin.expense.filter');
    Route::post('expense', [ExpenseController::class, 'store']);
    Route::get('expense/{id}', [ExpenseController::class, 'edit']);
    Route::put('expense/{id}', [ExpenseController::class, 'update']); 
    Route::get('expense-voucher/{id}', [ExpenseController::class, 'voucher'])->name('admin.expense.voucher');

    // ledger
    Route::get('ledger-accounts', [LedgerController::class, 'showLedgerAccounts'])->name('admin.ledgeraccount');
    Route::get('ledger/asset-details/{id}', [LedgerController::class, 'asset']);
    Route::get('ledger/expense-details/{id}', [LedgerController::class, 'expense']);
    Route::get('ledger/income-details/{id}', [LedgerController::class, 'income']);
    Route::get('ledger/liability-details/{id}', [LedgerController::class, 'liability']);
    Route::get('ledger/equity-details/{id}', [LedgerController::class, 'equity']);
    Route::get('ledger/vendor/{id}', [VendorLedgerController::class, 'vendor'])->name('admin.vendorledger');


    // pl statement
    Route::get('profit-statement', [PLStatementController::class, 'profitAndLossStatement'])->name('admin.profitAndLossStatement');


    Route::get('cash-sheet', [CashSheetController::class, 'cashSheet'])->name('admin.cashSheet');
    Route::post('cash-sheet', [CashSheetController::class, 'cashSheet'])->name('admin.cashSheet.Search');
    Route::post('cash-sheet/export', [CashSheetController::class, 'downloadExcel'])->name('admin.cashSheet.export');

    // roles and permission
    Route::get('role', [RoleController::class, 'index'])->name('admin.role');
    Route::post('role', [RoleController::class, 'store'])->name('admin.rolestore');
    Route::get('role/{id}', [RoleController::class, 'edit'])->name('admin.roleedit');
    Route::post('role-update', [RoleController::class, 'update'])->name('admin.roleupdate');

    // Balance Sheet
    Route::get('balance-sheet', [FinancialStatementController::class, 'balanceSheet'])->name('admin.balancesheet');
    Route::post('balance-sheet', [FinancialStatementController::class, 'balanceSheetReport'])->name('admin.balancesheet.report');

    // income statement
    Route::get('income-statement', [IncomeStatementController::class, 'incomeStatement'])->name('admin.incomestatement');
    Route::post('income-statement', [IncomeStatementController::class, 'incomeStatementSearch'])->name('admin.incomestatement.report');


    Route::get('program-detail-logs', [ProgramController::class, 'programDetailLogs'])->name('program.detail.logs');

    
    // Trial balance
    Route::get('trial-balance', [TrialBalanceController::class, 'trialBalance'])->name('admin.trialBalance');

    // Original Bill routes
    Route::get('/excel-upload', [ExcelUploadController::class, 'index'])->name('excel.upload');
    Route::get('/excel-template', [ExcelUploadController::class, 'exportTemplate'])->name('excel.template');
    Route::post('/excel-upload/store', [ExcelUploadController::class, 'store'])->name('excel.store');

    // Fuel Bill routes
    Route::get('/fuel-excel-upload', [ExcelUploadController::class, 'fuelIndex'])->name('fuel.excel.upload');
    Route::get('/fuel-excel-template', [ExcelUploadController::class, 'fuelExportTemplate'])->name('fuel.excel.template');
    Route::post('/fuel-excel-upload/store', [ExcelUploadController::class, 'fuelStore'])->name('fuel.excel.store');

    // Client Bill routes
    Route::get('/client-excel-upload', [ExcelUploadController::class, 'clientIndex'])->name('client.excel.upload');
    Route::get('/client-excel-template', [ExcelUploadController::class, 'clientExportTemplate'])->name('client.excel.template');
    Route::post('/client-excel-upload/store', [ExcelUploadController::class, 'clientStore'])->name('client.excel.store');

    
    Route::get('/vendor-rate-upload', [ExcelUploadController::class, 'vendorRateIndex'])->name('vendor.slabrate.upload');
    Route::post('/vendor-rate-upload', [ExcelUploadController::class, 'carryingBillUpdate'])->name('vendor.slabrate.store');
    Route::post('/program-details-qty-restore', [ExcelUploadController::class, 'programDetailsQtyUpdate'])->name('programDetailsQtyUpdate');

    
    Route::get('/check-activity-log', [ExcelUploadController::class, 'activityLog'])->name('log.activityLog');

});
  