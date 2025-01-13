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
use App\Http\Controllers\Admin\PLStatementController;

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


    Route::get('/mother-vassel', [MotherVasselController::class, 'index'])->name('admin.mothervassel');
    Route::post('/mother-vassel', [MotherVasselController::class, 'store'])->name('admin.mothervassel.store');
    Route::get('/mother-vassel/{id}/edit', [MotherVasselController::class, 'edit']);
    Route::post('/mother-vassel-update', [MotherVasselController::class, 'update']);
    Route::get('/mother-vassel/{id}', [MotherVasselController::class, 'delete']);


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

    
    Route::get('/vendor', [VendorController::class, 'index'])->name('admin.vendor');
    Route::get('/get-vendors-list/{id}', [VendorController::class, 'getVendorListByClientId'])->name('admin.getVendorListByClientId');
    Route::get('/get-vendors-sequence-list/{id}', [VendorController::class, 'getVendorWiseProgramList'])->name('admin.vendor.sequence.show');
    Route::post('/vendor', [VendorController::class, 'store']);
    Route::get('/vendor/{id}/edit', [VendorController::class, 'edit']);
    Route::post('/vendor-update', [VendorController::class, 'update']);
    Route::get('/vendor/{id}', [VendorController::class, 'delete']);
    Route::post('/add-vendor-sequence', [VendorController::class,'addSequenceNumber'])->name('addSequenceNumber');
    Route::post('/get-vendor-sequence', [VendorController::class,'getSequenceNumber']);
    Route::get('/vendor-sequence/{id}', [VendorController::class, 'sequencedelete']);
    
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
    Route::get('/program/{id}', [ProgramController::class, 'programDetail'])->name('admin.programDetail');
    Route::get('/program-vendor/{id}', [ProgramController::class, 'programVendor'])->name('admin.programVendorList');
    Route::get('/program-edit/{id}', [ProgramController::class, 'programEdit'])->name('admin.programEdit');
    Route::get('/add-program', [ProgramController::class, 'createProgram'])->name('admin.addProgram');
    Route::get('/after-challan-rcv', [ProgramController::class, 'afterPostProgram'])->name('admin.afterPostProgram');
    Route::post('/check-challan', [ProgramController::class, 'checkChallan'])->name('admin.checkChallan');
    Route::post('/check-slab-rate', [ProgramController::class, 'checkSlabRate'])->name('admin.checkSlabRate');
    Route::get('/program-delete/{id}', [ProgramController::class, 'prgmDelete']);
    Route::post('/add-program', [ProgramController::class, 'store'])->name('programStore');
    Route::post('/add-more-challan', [ProgramController::class, 'addMoreChallan'])->name('addMoreChallan');
    Route::post('/update-program', [ProgramController::class, 'programUpdate'])->name('programUpdate');
    Route::post('/get-vendor-advance-by-date', [ProgramController::class, 'getVendorAdvanceByDate'])->name('getAdvancePayments');

    // program after challan store
    Route::post('/program-after-challan-store', [ProgramController::class, 'afterPostProgramStore'])->name('after-challan-store');

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
    Route::get('/before-posting-challan-report', [ReportController::class, 'challanPostingVendorReport'])->name('challanPostingVendorReport');
    Route::post('/before-posting-challan-report', [ReportController::class, 'challanPostingVendorReport'])->name('challanPostingVendorReportshow');


    // bill generating
    Route::get('/bill-generating/{id}', [GeneratingBillController::class, 'billGenerating'])->name('billGenerating');
    Route::get('/generating-bill-show/{id}', [GeneratingBillController::class, 'billGeneratingShow'])->name('generatingBillShow');
    Route::post('/bill-generating', [GeneratingBillController::class, 'billGeneratingStore'])->name('billGeneratingStore');
    Route::get('/export-template', [GeneratingBillController::class, 'exportTemplate'])->name('export.template');

    
    // daybook
    Route::get('cash-book', [DaybookController::class, 'cashbook'])->name('admin.cashbook');
    Route::post('cash-book', [DaybookController::class, 'cashbook'])->name('admin.cashbookSearch');
    Route::get('bank-book', [DaybookController::class, 'bankbook'])->name('admin.bankbook');
    Route::post('bank-book', [DaybookController::class, 'bankbook'])->name('admin.bankbookSearch');


    //Chart of account
    Route::get('chart-of-account', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount');
    Route::post('chart-of-accounts', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount.filter');
    Route::post('chart-of-account', [ChartOfAccountController::class, 'store']);
    Route::get('chart-of-account/{id}', [ChartOfAccountController::class, 'edit']);
    Route::put('chart-of-account/{id}', [ChartOfAccountController::class, 'update']);
    Route::get('chart-of-account/{id}/change-status', [ChartOfAccountController::class, 'changeStatus']);


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

        // ledger
        Route::get('ledger-accounts', [LedgerController::class, 'showLedgerAccounts'])->name('admin.ledgeraccount');
        Route::get('ledger/asset-details/{id}', [LedgerController::class, 'asset']);
        Route::get('ledger/expense-details/{id}', [LedgerController::class, 'expense']);
        Route::get('ledger/income-details/{id}', [LedgerController::class, 'income']);
        Route::get('ledger/liability-details/{id}', [LedgerController::class, 'liability']);
        Route::get('ledger/equity-details/{id}', [LedgerController::class, 'equity']);


        // pl statement
        Route::get('profit-statement', [PLStatementController::class, 'profitAndLossStatement'])->name('admin.profitAndLossStatement');





});
  