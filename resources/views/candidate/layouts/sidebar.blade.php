<div class="items-stretch ps-xl-7 px-2 pe-xl-0 flex">
    <ul class="flex-wrap xl:flex horizontal-menu flex space-x-1 flex- flex block">
        <li class="flex space-x-8-item {{ Request::is("candidate/dashboard*') ? 'active' : '' }}">
            <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('dashboard') }}">
                <span class="horizontal-menu-icon"><i class="fab fa-dashcube"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.candidate.dashboard') }}</span>
            </a>
        </li>
        <li class="flex space-x-8-item {{ Request::is("candidate/profile*') ? 'active' : '' }}">
            <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('candidate.dashboard') }}">
                <span class="horizontal-menu-icon"><i class="far fa-user-circle"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.profile') }}</span>
            </a>
        </li>
        <li class="flex space-x-8-item {{ Request::is("candidate/favourite-jobs*') ? 'active' : '' }}">
            <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('favourite.jobs') }}">
                <span class="horizontal-menu-icon"><i class="far fa-star"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.favourite_jobs') }}</span>
            </a>
        </li>
        <li class="flex space-x-8-item {{ Request::is("candidate/favourite-companies*') ? 'active' : '' }}">
            <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
               href="{{ route('favourite.companies') }}">
                <span class="horizontal-menu-icon"><i class="far fa-building"></i></span>
                <span class="horizontal-menu-title">{{ __('messages.favourite_companies') }}</span>
            </a>
        </li>
        @if(getCurrentLanguageCode() == 'de' || getCurrentLanguageCode() == 'tr' || getCurrentLanguageCode() == 'pt' || getCurrentLanguageCode() == 'ru' || getCurrentLanguageCode() == 'es' || getCurrentLanguageCode() == 'fr')
            <li class="text-left text-left relative inline-block flex space-x-8-item hidden d-xl-grid relative inline-block -hover {{ Request::is("candidate/applied-job*','candidate/job-rounded-md p-4s*') ? 'active' : '' }}">
                <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3 ps-2" aria-current="page"
                   href="javascript:void(0)">
                    <span class="horizontal-menu-icon"><i class="fas fa-ellipsis-vertical fs-4"></i></span>
                </a>
                <ul class="shadow rounded mt-2 bg-white horizontal-submenu origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 top-100">
                    <li>
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("candidate/applied-job*') ? 'active' : '' }}"
                           href="{{ route('candidate.dashboard') }}">
                            <span class="horizontal-menu-icon me-1"><i class="fas fa-briefcase fs-6"></i></span>
                            <span class="horizontal-menu-title fs-6">{{ __('messages.applied_job.applied_jobs') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("candidate/job-rounded-md p-4s*') ? 'active' : '' }}"
                           href="{{ route('candidate.dashboard') }}">
                            <span class="horizontal-menu-icon me-1"><i class="far fa-bell fs-6"></i></span>
                            <span class="horizontal-menu-title fs-6">{{ __('messages.job.job_rounded-md p-4') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- start side bar menu for bar --}}
            <li class="xl:hidden flex space-x-8-item {{ Request::is("candidate/applied-job*') ? 'active' : '' }}">
                <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
                   href="{{ route('candidate.dashboard') }}">
                    <span class="horizontal-menu-icon me-1"><i class="fas fa-briefcase"></i></span>
                    <span class="horizontal-menu-title">{{ __('messages.applied_job.applied_jobs') }}</span>
                </a>
            </li>
            <li class="xl:hidden flex space-x-8-item {{ Request::is("candidate/job-rounded-md p-4s*') ? 'active' : '' }}">
                <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
                   href="{{ route('candidate.dashboard') }}">
                    <span class="horizontal-menu-icon me-1"><i class="far fa-bell"></i></span>
                    <span class="horizontal-menu-title">{{ __('messages.job.job_rounded-md p-4') }}</span>
                </a>
            </li>
            {{-- end side bar menu for bar --}}
        @else
            <li class="flex space-x-8-item {{ Request::is("candidate/applied-job*') ? 'active' : '' }}">
                <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
                   href="{{ route('candidate.dashboard') }}">
                    <span class="horizontal-menu-icon"><i class="fas fa-briefcase"></i></span>
                    <span class="horizontal-menu-title">{{ __('messages.applied_job.applied_jobs') }}</span>
                </a>
            </li>
            <li class="flex space-x-8-item {{ Request::is("candidate/job-rounded-md p-4s*') ? 'active' : '' }}">
                <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium flex items-center py-3" aria-current="page"
                   href="{{ route('candidate.dashboard') }}">
                    <span class="horizontal-menu-icon"><i class="far fa-bell"></i></span>
                    <span class="horizontal-menu-title">{{ __('messages.job.job_rounded-md p-4') }}</span>
                </a>
            </li>
        @endif
    </ul>
</div>
