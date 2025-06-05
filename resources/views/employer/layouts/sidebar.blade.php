<div class="ps-xl-7 px-2 pe-xl-0 flex align-items-stretch">
    <ul class="horizontal-menu flex space-x-1 flex- flex flex-wrap block d-xl-flex">
        <li class="nav-item {{ Request::is("employer/dashboard*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('employer.dashboard') }}">
                <span class="horizontal-menu-icon"><i class="fab fa-dashcube"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.dashboard') }}</span>
            </a>
        </li>
        <li class="nav-item {{ \Illuminate\Support\Facades\Route::is("company.edit.form') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('company.edit.form', \Illuminate\Support\Facades\Auth::user()->owner_id) }}">
                <span class="horizontal-menu-icon"><i class="far fa-user-circle"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.employer_menu.employer_profile') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is("employer/jobs*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('job.index') }}">
                <span class="horizontal-menu-icon"><i class="far fa-star"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.employer_menu.jobs') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is("employer/job-stage*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('admin.job-stages.index') }}">
                <span class="horizontal-menu-icon"><i class="far fa-building"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.job_stages') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is("employer/followers*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('followers.index') }}">
                <span class="horizontal-menu-icon"><i class="fas fa-briefcase"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.employer_menu.followers') }}</span>
            </a>
        </li>
        <li class="nav-item hidden d-xl-grid relative inline-block text-left dropdown-hover {{ Request::is("employer/transactions*','employer/manage-subscription*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3 ps-2" aria-current="page"
               href="javascript:void(0)">
                <span class="horizontal-menu-icon"><i class="fas fa-ellipsis-vertical fs-4"></i></span>
            </a>
            <ul class="horizontal-submenu origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 top-100">
                <li>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("employer/transaction*') ? 'active' : '' }}"
                       href="{{ route('transactions.index') }}">
                        <span class="horizontal-menu-icon me-1"><i class="fas fa-money-bill fs-6"></i></span>
                        <span class="horizontal-menu-title fs-6">{{ __('messages.employer_menu.transactions') }}</span>
                    </a>
                </li>
                <li>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("employer/manage-subscription*') ? 'active' : '' }}"
                       href="{{ route('manage-subscription.index') }}">
                        <span class="horizontal-menu-icon me-1"><i class="fab fa-bandcamp fs-6"></i></span>
                        <span class="horizontal-menu-title fs-6">{{ __('messages.employer_menu.manage_subscriptions') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{ -- start side bar menu for bar-- }}
        <li class="nav-item d-xl-none {{ Request::is("employer/transaction*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('transactions.index') }}">
                <span class="horizontal-menu-icon me-1"><i class="fas fa-money-bill"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.employer_menu.transactions') }}</span>
            </a>
        </li>
        <li class="nav-item d-xl-none {{ Request::is("employer/manage-subscription*') ? 'active' : '' }}">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('manage-subscription.index') }}">
                <span class="horizontal-menu-icon me-1"><i class="fab fa-bandcamp"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.employer_menu.manage_subscriptions') }}</span>
            </a>
        </li>
        {{ -- end side bar menu for bar-- }}
    </ul>
</div>
