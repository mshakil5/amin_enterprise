<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
      
           
      <li class="nav-item">
        <a href="{{route('admin.dashboard')}}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Dashboard
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('alladmin')}}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Admin
          </p>
        </a>
      </li>
      {{-- <li class="nav-item">
        <a href="{{route('admin.agent')}}" class="nav-link {{ (request()->is('admin/agent*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Agent
          </p>
        </a>
      </li> --}}
      <li class="nav-item">
        <a href="{{route('admin.country')}}" class="nav-link {{ (request()->is('admin/country*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Country
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('admin.mothervassel')}}" class="nav-link {{ (request()->is('admin/mother-vassel*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Mother Vassel
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('lightervassel')}}" class="nav-link {{ (request()->is('admin/lighter-vassel*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Lighter Vassel
          </p>
        </a>
      </li>

      
      <li class="nav-item">
        <a href="{{route('admin.ghat')}}" class="nav-link {{ (request()->is('admin/ghat*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Ghat
          </p>
        </a>
      </li>

            
      <li class="nav-item">
        <a href="{{route('admin.pump')}}" class="nav-link {{ (request()->is('admin/pump*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Petrol Pump
          </p>
        </a>
      </li>

      
      <li class="nav-item">
        <a href="{{route('admin.vendor')}}" class="nav-link {{ (request()->is('admin/vendor*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Vendor
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.client')}}" class="nav-link {{ (request()->is('admin/client')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Client
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.destination')}}" class="nav-link {{ (request()->is('admin/destination*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Destination
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.slabrate')}}" class="nav-link {{ (request()->is('admin/slab-rate*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
           Vendor Slab Rate
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.clientrate')}}" class="nav-link {{ (request()->is('admin/client-rate*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Client Bill Rate
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.getBill')}}" class="nav-link {{ (request()->is('admin/bill*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Bill Received
          </p>
        </a>
      </li>

      <li class="nav-item {{ (request()->is('admin/program*')) ||  (request()->is('admin/add-program')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Program
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item  ">
            <a href="{{route('admin.addProgram')}}" class="nav-link {{ (request()->is('admin/add-program')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Add Program</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('admin.allProgram')}}" class="nav-link {{ (request()->is('admin/program*')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Programs</p>
            </a>
          </li>
          
        </ul>
      </li>

      {{-- <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Bill Generator
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{route('billGenerating')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Uploads</p>
            </a>
          </li>
          
        </ul>
      </li> --}}

      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Report
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{route('challanPostingVendorReport')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Challan Posting Report</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('vendorLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Vendor Ledger</p>
            </a>
          </li>
          
          

          
          
        </ul>
      </li>

      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Ledger
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{route('receivableLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Receivable Ledger</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="{{route('receivableLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Payable Ledger</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('advanceLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Advance Ledger</p>
            </a>
          </li>

          {{-- <li class="nav-item">
            <a href="{{route('receivableLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Expense Ledger</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('receivableLedger')}}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Income Ledger</p>
            </a>
          </li> --}}

          
          
        </ul>
      </li>

      <a href="{{ route('admin.ledgeraccount') }}" class="nav-link {{ (request()->is('admin/ledger-accounts*')) ? 'active' : '' }}">
          <i class="fa fa-users"></i>
          <p>Ledger</p>
      </a>

      
      <li class="nav-item">
        <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ (request()->is('admin/chart-of-account*')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Chart Of Accounts</p>
        </a>
      </li>
      
      <li class="nav-item">
        <a href="{{ route('admin.income') }}" class="nav-link {{ (request()->is('admin/income')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Income</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.expense') }}" class="nav-link {{ (request()->is('admin/expense*')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Expense</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.asset') }}" class="nav-link {{ (request()->is('admin/asset*')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Assets</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.liabilities') }}" class="nav-link {{ (request()->is('admin/liabilities*')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Liabilities</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.equity') }}" class="nav-link {{ (request()->is('admin/equity*')) ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <p>Equity</p>
        </a>
    </li>

      <li class="nav-item  {{ (request()->is('admin/cash-book')) ? 'menu-open' : '' }}{{ (request()->is('admin/bank-book')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('admin/cash-book')) ? 'active' : '' }}{{ (request()->is('admin/bank-book')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Day Book
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{route('admin.cashbook')}}" class="nav-link {{ (request()->is('admin/cash-book')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Cash Book</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('admin.bankbook')}}" class="nav-link {{ (request()->is('admin/bank-book')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Bank Book</p>
            </a>
          </li>
          
        </ul>
      </li>


      
      <li class="nav-item">
        <a href="{{ route('admin.profitAndLossStatement') }}" class="nav-link {{ (request()->is('admin/profit-statement')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            P/L Statement
          </p>
        </a>
      </li>

      {{-- <li class="nav-item">
        <a href="#" class="nav-link ">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Financial Statement
          </p>
        </a>
      </li> --}}
      
      
      
      
      <span class="mb-5"></span>
      <span class="mb-5"></span>
      <span class="mb-5"></span>
      
      {{-- <li class="nav-item {{ (request()->is('admin/client*')) ? 'menu-open' : '' }}{{ (request()->is('admin/completed-clients*')) ? 'menu-open' : '' }}{{ (request()->is('admin/decline-clients*')) ? 'menu-open' : '' }}{{ (request()->is('admin/processing-clients*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('admin/client*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-copy"></i>
          <p>
            Clients
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{route('admin.processingclient')}}" class="nav-link {{ (request()->is('admin/processing-clients*')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Processing</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('admin.completedclient')}}" class="nav-link {{ (request()->is('admin/completed-clients*')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Complete</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('admin.declineclient')}}" class="nav-link {{ (request()->is('admin/decline-clients*')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Decline</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="{{route('admin.client')}}" class="nav-link {{ (request()->is('admin/client*')) ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>All Clients</p>
            </a>
          </li>

        </ul>
      </li> --}}


      






      {{-- <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>
            Charts
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/charts/chartjs.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>ChartJS</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/charts/flot.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Flot</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/charts/inline.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Inline</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/charts/uplot.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>uPlot</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-tree"></i>
          <p>
            UI Elements
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/UI/general.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>General</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/icons.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Icons</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/buttons.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Buttons</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/sliders.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Sliders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/modals.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Modals & Alerts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/navbar.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Navbar & Tabs</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/timeline.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Timeline</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/UI/ribbons.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Ribbons</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-edit"></i>
          <p>
            Forms
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/forms/general.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>General Elements</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/forms/advanced.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Advanced Elements</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/forms/editors.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Editors</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/forms/validation.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Validation</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-table"></i>
          <p>
            Tables
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/tables/simple.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Simple Tables</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/tables/data.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>DataTables</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/tables/jsgrid.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>jsGrid</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-header">EXAMPLES</li>
      <li class="nav-item">
        <a href="pages/calendar.html" class="nav-link">
          <i class="nav-icon far fa-calendar-alt"></i>
          <p>
            Calendar
            <span class="badge badge-info right">2</span>
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="pages/gallery.html" class="nav-link">
          <i class="nav-icon far fa-image"></i>
          <p>
            Gallery
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="pages/kanban.html" class="nav-link">
          <i class="nav-icon fas fa-columns"></i>
          <p>
            Kanban Board
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon far fa-envelope"></i>
          <p>
            Mailbox
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/mailbox/mailbox.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Inbox</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/mailbox/compose.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Compose</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/mailbox/read-mail.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Read</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-book"></i>
          <p>
            Pages
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/examples/invoice.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Invoice</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/profile.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Profile</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/e-commerce.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>E-commerce</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/projects.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Projects</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/project-add.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Project Add</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/project-edit.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Project Edit</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/project-detail.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Project Detail</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/contacts.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Contacts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/faq.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>FAQ</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/contact-us.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Contact us</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon far fa-plus-square"></i>
          <p>
            Extras
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Login & Register v1
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages/examples/login.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Login v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/register.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Register v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/forgot-password.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Forgot Password v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/recover-password.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Recover Password v1</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Login & Register v2
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages/examples/login-v2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Login v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/register-v2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Register v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/forgot-password-v2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Forgot Password v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/examples/recover-password-v2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Recover Password v2</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="pages/examples/lockscreen.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Lockscreen</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/legacy-user-menu.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Legacy User Menu</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/language-menu.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Language Menu</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/404.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Error 404</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/500.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Error 500</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/pace.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Pace</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/examples/blank.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Blank Page</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="starter.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Starter Page</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-search"></i>
          <p>
            Search
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="pages/search/simple.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Simple Search</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/search/enhanced.html" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Enhanced</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-header">MISCELLANEOUS</li>
      <li class="nav-item">
        <a href="iframe.html" class="nav-link">
          <i class="nav-icon fas fa-ellipsis-h"></i>
          <p>Tabbed IFrame Plugin</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="https://adminlte.io/docs/3.1/" class="nav-link">
          <i class="nav-icon fas fa-file"></i>
          <p>Documentation</p>
        </a>
      </li>
      <li class="nav-header">MULTI LEVEL EXAMPLE</li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-circle nav-icon"></i>
          <p>Level 1</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-circle"></i>
          <p>
            Level 1
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Level 2</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Level 2
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Level 3</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Level 3</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Level 3</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Level 2</p>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-circle nav-icon"></i>
          <p>Level 1</p>
        </a>
      </li>
      <li class="nav-header">LABELS</li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon far fa-circle text-danger"></i>
          <p class="text">Important</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon far fa-circle text-warning"></i>
          <p>Warning</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon far fa-circle text-info"></i>
          <p>Informational</p>
        </a>
      </li> --}}
    </ul>
  </nav>