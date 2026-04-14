<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        {{-- =============================================
             MAIN DASHBOARD
             ============================================= --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- =============================================
             OPERATIONS & LOGISTICS
             ============================================= --}}
        @if(in_array('4', json_decode(auth()->user()->role->permission)) || 
           in_array('5', json_decode(auth()->user()->role->permission)) || 
           in_array('6', json_decode(auth()->user()->role->permission)) || 
           in_array('7', json_decode(auth()->user()->role->permission)) || 
           in_array('10', json_decode(auth()->user()->role->permission)))
        <li class="nav-header">OPERATIONS</li>
        <li class="nav-item {{ (request()->is('admin/mother-vassel*') || request()->is('admin/lighter-vassel*') || request()->is('admin/ghat*') || request()->is('admin/pump*') || request()->is('admin/destination*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/mother-vassel*') || request()->is('admin/lighter-vassel*') || request()->is('admin/ghat*') || request()->is('admin/pump*') || request()->is('admin/destination*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-route"></i>
                <p>Logistics & Sites <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('4', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.mothervassel') }}" class="nav-link {{ (request()->is('admin/mother-vassel*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Mother Vessel</p>
                    </a>
                </li>
                @endif
                @if(in_array('5', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('lightervassel') }}" class="nav-link {{ (request()->is('admin/lighter-vassel*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lighter Vessel</p>
                    </a>
                </li>
                @endif
                @if(in_array('6', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.ghat') }}" class="nav-link {{ (request()->is('admin/ghat*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Ghat / Yard</p>
                    </a>
                </li>
                @endif
                @if(in_array('7', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.pump') }}" class="nav-link {{ (request()->is('admin/pump*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Petrol Pump</p>
                    </a>
                </li>
                @endif
                @if(in_array('10', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.destination') }}" class="nav-link {{ (request()->is('admin/destination*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Destinations</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- ==============================================
             PEOPLE & ENTITIES
             ============================================= --}}
        @if(in_array('2', json_decode(auth()->user()->role->permission)) || 
           in_array('8', json_decode(auth()->user()->role->permission)) || 
           in_array('9', json_decode(auth()->user()->role->permission)))
        <li class="nav-header">PEOPLE & ENTITIES</li>
        <li class="nav-item {{ (request()->is('admin/new-admin*') || request()->is('admin/vendor*') || request()->is('admin/client')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/new-admin*') || request()->is('admin/vendor*') || request()->is('admin/client')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <p>Entities <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('2', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('alladmin') }}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Admin Users</p>
                    </a>
                </li>
                @endif
                @if(in_array('8', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.vendor') }}" class="nav-link {{ (request()->is('admin/vendor*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Vendors</p>
                    </a>
                </li>
                @endif
                @if(in_array('9', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.client') }}" class="nav-link {{ (request()->is('admin/client')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Clients</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- ==============================================
             PROGRAM & BILLING
             ============================================= --}}
        @if(in_array('11', json_decode(auth()->user()->role->permission)) || 
           in_array('12', json_decode(auth()->user()->role->permission)) || 
           in_array('13', json_decode(auth()->user()->role->permission)) || 
           in_array('14', json_decode(auth()->user()->role->permission)))
        <li class="nav-header">PROGRAM & BILLING</li>
        
        @if(in_array('14', json_decode(auth()->user()->role->permission)))
        <li class="nav-item {{ (request()->is('admin/program*')) || (request()->is('admin/after-challan-rcv')) || (request()->is('admin/add-program')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/program*') || request()->is('admin/after-challan-rcv') || request()->is('admin/add-program')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Programs <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.addProgram') }}" class="nav-link {{ (request()->is('admin/add-program')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Before Challan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.afterPostProgram') }}" class="nav-link {{ (request()->is('admin/after-challan-rcv')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>After Challan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.allProgram') }}" class="nav-link {{ (request()->is('admin/program*') && !request()->is('admin/add-program') && !request()->is('admin/after-challan-rcv')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>All Programs</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <li class="nav-item {{ (request()->is('admin/bill*') || request()->is('admin/slab-rate*') || request()->is('admin/client-rate*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/bill*') || request()->is('admin/slab-rate*') || request()->is('admin/client-rate*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                <p>Billing & Rates <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('13', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.getBill') }}" class="nav-link {{ (request()->is('admin/bill*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bill Received</p>
                    </a>
                </li>
                @endif
                @if(in_array('11', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.slabrate') }}" class="nav-link {{ (request()->is('admin/slab-rate*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Vendor Slab Rate</p>
                    </a>
                </li>
                @endif
                @if(in_array('12', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.clientrate') }}" class="nav-link {{ (request()->is('admin/client-rate*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Client Bill Rate</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- ==============================================
             ACCOUNTING & FINANCE
             ============================================= --}}
        @if(in_array('3', json_decode(auth()->user()->role->permission)) || 
           in_array('17', json_decode(auth()->user()->role->permission)) || 
           in_array('18', json_decode(auth()->user()->role->permission)) || 
           in_array('19', json_decode(auth()->user()->role->permission)) || 
           in_array('20', json_decode(auth()->user()->role->permission)) || 
           in_array('21', json_decode(auth()->user()->role->permission)) || 
           in_array('22', json_decode(auth()->user()->role->permission)) || 
           in_array('23', json_decode(auth()->user()->role->permission)) || 
           in_array('24', json_decode(auth()->user()->role->permission)) || 
           in_array('25', json_decode(auth()->user()->role->permission)) || 
           in_array('27', json_decode(auth()->user()->role->permission)))
        <li class="nav-header">ACCOUNTING & FINANCE</li>

        {{-- Transactions Dropdown --}}
        <li class="nav-item {{ (request()->is('admin/income') || request()->is('admin/expense*') || request()->is('admin/asset*') || request()->is('admin/liabilities*') || request()->is('admin/equity*') || request()->is('admin/petty-cash*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/income') || request()->is('admin/expense*') || request()->is('admin/asset*') || request()->is('admin/liabilities*') || request()->is('admin/equity*') || request()->is('admin/petty-cash*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-exchange-alt"></i>
                <p>Transactions <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('19', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.income') }}" class="nav-link {{ (request()->is('admin/income')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-success"></i><p>Income</p>
                    </a>
                </li>
                @endif
                @if(in_array('20', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.expense') }}" class="nav-link {{ (request()->is('admin/expense*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-danger"></i><p>Expense</p>
                    </a>
                </li>
                @endif
                @if(in_array('21', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.asset') }}" class="nav-link {{ (request()->is('admin/asset*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-dark"></i><p>Assets</p>
                    </a>
                </li>
                @endif
                @if(in_array('22', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.liabilities') }}" class="nav-link {{ (request()->is('admin/liabilities*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-warning"></i><p>Liabilities</p>
                    </a>
                </li>
                @endif
                @if(in_array('23', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.equity') }}" class="nav-link {{ (request()->is('admin/equity*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-info"></i><p>Equity</p>
                    </a>
                </li>
                @endif
                @if(in_array('3', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.pettycash') }}" class="nav-link {{ (request()->is('admin/petty-cash*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-secondary"></i><p>Petty Cash</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- Accounts & Ledger Dropdown --}}
        <li class="nav-item {{ (request()->is('admin/account*') || request()->is('admin/chart-of-account*') || request()->is('admin/ledger-receivable') || request()->is('admin/ledger-payable') || request()->is('admin/ledger-advance')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/account*') || request()->is('admin/chart-of-account*') || request()->is('admin/ledger-receivable') || request()->is('admin/ledger-payable') || request()->is('admin/ledger-advance')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-sitemap"></i>
                <p>Accounts & Ledger <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.account') }}" class="nav-link {{ (request()->is('admin/account*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Account Types</p>
                    </a>
                </li>
                @if(in_array('18', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ (request()->is('admin/chart-of-account*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Chart of Accounts</p>
                    </a>
                </li>
                @endif
                @if(in_array('17', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.ledgeraccount') }}" class="nav-link {{ (request()->is('admin/ledger-accounts*')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>General Ledger</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receivableLedger') }}" class="nav-link {{ (request()->is('admin/ledger-receivable')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Receivable Ledger</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('payableLedger') }}" class="nav-link {{ (request()->is('admin/ledger-payable')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Payable Ledger</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('advanceLedger') }}" class="nav-link {{ (request()->is('admin/ledger-advance')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Advance Ledger</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- Books & Statements Dropdown --}}
        <li class="nav-item {{ (request()->is('admin/day-book') || request()->is('admin/cash-book') || request()->is('admin/bank-book') || request()->is('admin/cash-sheet') || request()->is('admin/profit-statement') || request()->is('admin/trial-balance')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/day-book') || request()->is('admin/cash-book') || request()->is('admin/bank-book') || request()->is('admin/cash-sheet') || request()->is('admin/profit-statement') || request()->is('admin/trial-balance')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-book-open"></i>
                <p>Books & Statements <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('24', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.daybook') }}" class="nav-link {{ (request()->is('admin/day-book')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Day Book</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.cashbook') }}" class="nav-link {{ (request()->is('admin/cash-book')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Cash Book</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.bankbook') }}" class="nav-link {{ (request()->is('admin/bank-book')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bank Book</p>
                    </a>
                </li>
                @endif
                @if(in_array('27', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.cashSheet') }}" class="nav-link {{ (request()->is('admin/cash-sheet')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Cash Sheet</p>
                    </a>
                </li>
                @endif
                @if(in_array('25', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.profitAndLossStatement') }}" class="nav-link {{ (request()->is('admin/profit-statement')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>P/L Statement</p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('admin.trialBalance') }}" class="nav-link {{ (request()->is('admin/trial-balance')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Trial Balance</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ==============================================
             REPORTS
             ============================================= --}}
        @if(in_array('15', json_decode(auth()->user()->role->permission)))
        <li class="nav-header">REPORTS</li>
        <li class="nav-item {{ (request()->is('admin/report*') || request()->is('admin/vendor-ledger')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('admin/report*') || request()->is('admin/vendor-ledger')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Standard Reports <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('challanPostingVendorReport') }}" class="nav-link {{ (request()->is('admin/report-challan')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Challan Posting Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('vendorLedger') }}" class="nav-link {{ (request()->is('admin/vendor-ledger')) ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Vendor Ledger Report</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ==============================================
             SYSTEM & SETTINGS
             ============================================= --}}
        <li class="nav-header">SYSTEM</li>
        
        <li class="nav-item">
            <a href="{{ route('program.detail.logs') }}" class="nav-link {{ (request()->is('admin/program-detail-logs*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>Program Logs</p>
            </a>
        </li>

        @if(in_array('26', json_decode(auth()->user()->role->permission)))
        <li class="nav-item">
            <a href="{{ route('admin.role') }}" class="nav-link {{ (request()->is('admin/role*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Roles & Permissions</p>
            </a>
        </li>
        @endif

        <li class="nav-item d-none {{ Route::is('excel.upload') || Route::is('excel.template') || Route::is('excel.store') || 
                    Route::is('fuel.excel.upload') || Route::is('fuel.excel.template') || Route::is('fuel.excel.store') ||
                    Route::is('client.excel.upload') || Route::is('client.excel.template') || Route::is('client.excel.store') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('excel.*') || Route::is('fuel.excel.*') || Route::is('client.excel.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-excel"></i>
                <p>
                    Excel Uploads
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('excel.upload') }}" class="nav-link {{ Route::is('excel.upload') || Route::is('excel.template') || Route::is('excel.store') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bill Upload</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('fuel.excel.upload') }}" class="nav-link {{ Route::is('fuel.excel.upload') || Route::is('fuel.excel.template') || Route::is('fuel.excel.store') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Fuel Bill Upload</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('client.excel.upload') }}" class="nav-link {{ Route::is('client.excel.upload') || Route::is('client.excel.template') || Route::is('client.excel.store') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Client Bill Upload</p>
                    </a>
                </li>
            </ul>
        </li>

        <span class="mb-5"></span>
        <span class="mb-5"></span>
        <span class="mb-5"></span>

    </ul>
</nav>