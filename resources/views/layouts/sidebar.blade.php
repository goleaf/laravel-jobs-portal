{{ -- <link href=" mix('assets/css/style.css') " rel="stylesheet" type="text/css"/> -- }}
<div class="aside-menu- max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto" id="sidebar">
    <!--begin::Brand-->
    <div class="aside-menu-container__aside-logo flex-column-auto">
        <a data-turbo="false" href="{{ url('/') }}" target="_blank" data-toggle="tooltip" data-placement="right"
           class="text-decoration-none sidebar-logo image image-mini"
           title="{{ getAppName() }}">
            <img src="{{ getLogoUrl() }}"
                 alt="Logo" width="70px" height="30px" alt="Logo" class="img-fluid new-logo-image"/>
            <span class="bg-white shadow-sm brand-name text-gray-900 text-decoration-none logo ps-2">{{ getAppName() }}</span>
        </a>

        <button type="button" class="border border-gray-300 bg-transparent">
            <i class="fa-solid fa-bars fs-1"></i>
        </button>

    </div>
    <!--end::Brand-->
    <form class="flex relative aside-menu-container__aside-search search-control py-3 mt-1">
        <div class="relative w-full sidebar-search-box">
            <input class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" type="text" placeholder={{ __('messages.common.search') }} id="menuSearch" aria-label="Search" name="search">
            <span class="aside-menu-container__search-icon absolute flex items-center top-0 bottom-0">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>
    </form>
    <div class="no-record text-center hidden">{{ __('messages.common.no_found_record') }}</div>
    <div class="sidebar-scrolling overflow-auto">
        <ul class="aside-menu-container__aside-menu flex space-x-1 flex- flex-1">
            @include('layouts.menu')
        </ul>
    </div>
</div>
<div class="bg-overlay" id="sidebar-overly"></div>
