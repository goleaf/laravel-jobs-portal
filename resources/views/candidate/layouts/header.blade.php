@php($notifications = getNotification(\App\Models\Notification::CANDIDATE))
@php($notificationCount = $notifications->count())
<header class="items-stretch max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mx-auto px-4 mx-auto xxl flex justify-between">
    <div class="flex-wrap flex-wrap flex items-center flex-gflex -mx-4-1 flex-lg-gflex -mx-4-0">
        <a href="{{ route('front.home') }}" data-turbo="false" target="_blank"
           class="text-decoration-none horizontal-sidebar-logo flex items-center pe-xl-8">
            <div class="image image-mini me-3">
                <img src="{{ getLogoUrl() }}"
                     class="img-fluid" alt="profile image">
            </div>
            <span class="sm:block text-gray-900 fs-4 hidden"> {{ getAppName() }}</span>
        </a>
    </div>
    <div class="flex-wrap items-stretch flex justify-content-xl-between justify-end flex-gflex -mx-4-1">
        <nav class="shadow-sm shadow border bg-white shadow border bg-white bg-white xl:flex -b -gray-200 -expand-xl -sm light horizontal-sidebar block items-stretch py-3 py-xl-0"
             id="nav-header">
            @include('candidate.layouts.sidebar')
        </nav>
        <ul class="flex-nowrap items-stretch flex space-x-1">
            <li class="items-stretch px-xxl-3 px-2 flex">
                <a href="{{ route('theme.mode') }}" class="flex items-center" data-turbo="false">
                    <i class="fas user-check-icon {{ getLoggedInUser()->theme_mode ? 'fa-sun' : 'fa-moon' }} fs-2"></i>
                </a>
            </li>
            <li class="items-stretch px-xxl-3 px-2 flex">
                <div class="text-left text-left relative inline-block relative inline-block custom- flex items-center py-4">
                    <button class="transition duration-150 ease-in-out flex-1" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="text-indigo-600 fa-solid fa-bell -600 fs-2"></i>
                            @if($notificationCount > 0)
                                <span class="rounded rounded absolute notification-count top-0 start-100 translate-middle inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium-circle bg-red-600" id="counter">
                    {{ ($notificationCount) }}
                    <span class="visually-hidden">{{ __('messages.unread_messages') }}</span>
                            @endif
                    </button>
                    <div class="shadow rounded mt-2 bg-white origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="dropdownMenuButton1">
                        <div class="border text-start -bottom py-4 px-7">
                            <h3 class="mb-0 text-gray-900">{{ __('messages.notification.notifications') }}</h3>
                        </div>
                        <div class="mt-5 px-7 inner-scroll height-270">
                            @if($notificationCount > 0)
                                @foreach($notifications as $notification)
                                    <div class="mb-5 flex relative readNotification cursor-pointer"
                                         data-id="{{ $notification->id }}" id="readNotification">
                                                            <span class="text-indigo-600 me-5 -600 fs-2 icon-label">
                                                                <i class="{{ getNotificationIcon($notification->type) }}"></i></span>
                                        <div>
                                            <h5 class="mb-2 text-gray-900 fs-6">{{ $notification->title }}</h5>
                                            <h6 class="mb-0 text-gray-600 fs-small fw-light">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(null, true) }}</h6>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="mt-5 text-center empty-state fs-6 text-gray-800 fw-bold" data-height="400">
                                    <p>{{ __('messages.notification.empty_notifications') }}</p>
                                </div>
                            @endif
                            <div class="mt-5 text-center empty-state fs-6 text-gray-800 fw-bold hidden"
                                 data-height="400">
                                <p>{{ __('messages.notification.empty_notifications') }}</p>
                            </div>
                        </div>
                        @if($notificationCount > 0)
                            <div class="border p-4 text-center -top">
                                <h5 class="mb-0 text-indigo-600-600 fs-5 cursor-pointer"
                                    id="readAllNotification">{{ __('messages.notification.mark_all_as_read') }}</h5>
                            </div>
                        @endif
                    </div>

                </div>
            </li>

            <li class="items-stretch px-xxl-3 px-2 flex">
                <div class="text-left text-left relative inline-block items-stretch relative inline-block -transparent flex">
                    <button class="transition duration-150 ease-in-out flex-1" type="button"
                            id="dropdownMenuButton1" data-bs-auto-close="outside"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="image image-circle image-mini flex items-center me-sm-3">
                            <img src="{{ getLoggedInUser()->avatar }}"
                                 class="img-fluid" alt="profile image">
                        </div>
                        {{ \Illuminate\Support\Facades\Auth::user()->full_name }}
{{-- <i class="fa-solid fa-angle-down ms-2"></i> --}}
                    </button>
                    <div class="shadow rounded pb-4 mt-2 bg-white origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 py-7" aria-labelledby="dropdownMenuButton1"
                         data-bs-auto-close="outside">
                        <div class="border pb-5 text-center -bottom">
                            <div class="mb-5 image image-circle image-tiny">
                                <img src="{{ getLoggedInUser()->avatar }}" class="img-fluid" alt="profile image">
                            </div>
                            <h3 class="text-gray-900">{{ \Illuminate\Support\Facades\Auth::user()->full_name }}</h3>
                            <h4 class="mb-0 fw-400 fs-6">{{ \Illuminate\Support\Facades\Auth::user()->email }}</h4>
                        </div>
                        <ul class="pt-4">
                            <li>
                                <a href="javascript:void(0)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 editCandidateProfileModal"
                                   data-id="{{ getLoggedInUserId() }}">
                                     <span class="text-left relative inline-block -icon me-4 text-gray-600">
                                        <i class="fa-solid fa-user"></i>
                                     </span> {{ __('messages.user.edit_profile') }}</a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 changePasswordModal"
                                   href="javascript:void(0)"  data-id="{{ getLoggedInUserId() }}">
                                    <span class="text-left relative inline-block -icon me-4 text-gray-600">
                                        <i class="fa-solid fa-lock"></i>
                                    </span> {{ (Str::limit(__('messages.user.change_password'),20,'...')) }}
                                </a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 changeLanguageModal"
                                   href="javascript:void(0)" data-id="{{ getLoggedInUserId() }}">
                                    <span class="text-left relative inline-block -icon me-4 text-gray-600">
                                        <i class="fa-solid fa-globe"></i>
                                    </span> {{ (Str::limit(__('messages.user_language.change_language'),20,'...')) }}
                                </a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900" href="{{ url('logout') }}"
                                   onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                                    <span class="text-left relative inline-block -icon me-4 text-gray-600">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </span> {{ __('messages.user.logout') }}
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                                    
    @csrf
{{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="flex items-center">
                <button type="button" class="transition duration-150 ease-in-out flex-1">
                    <i class="fa-solid fa-bars fs-1"></i>
                </button>
            </li>
        </ul>
    </div>
</header>
<div class="bg-overlay" id="horizontal-menubar-overly"></div>
