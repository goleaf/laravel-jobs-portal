@php($notifications = getNotification(\App\Models\Notification::CANDIDATE))
@php($notificationCount = $notifications->count())
<header class="w-full container mx-auto px-4 mx-auto -xxl flex align-items-stretch justify-between">
    <div class="flex items-center flex-grow-1 flex-lg-grow-0">
        <a href="{{ route('front.home')  }}" data-turbo="false" target="_blank"
           class="text-decoration-none horizontal-sidebar-logo flex items-center pe-xl-8">
            <div class="image image-mini me-3">
                <img src="{{ getLogoUrl() }}"
                     class="img-fluid" alt="profile image">
            </div>
            <span class="text-gray-900 fs-4 hidden d-sm-block"> {{ getAppName()  }}</span>
        </a>
    </div>
    <div class="flex align-items-stretch justify-content-xl-between justify-end flex-grow-1">
        <nav class="bg-white shadow-sm border-b border-gray-200 navbar-expand-xl bg-white shadow-sm -light horizontal-sidebar d-xl-flex block align-items-stretch py-3 py-xl-0"
             id="nav-header">
            @include('candidate.layouts.sidebar')
        </nav>
        <ul class="flex space-x-1 align-items-stretch flex-nowrap">
            <li class="px-xxl-3 px-2 flex align-items-stretch">
                <a href="{{ route('theme.mode')  }}" class="flex items-center" data-turbo="false">
                    <i class="fas user-check-icon {{ getLoggedInUser()->theme_mode ? 'fa-sun' : 'fa-moon'  }} fs-2"></i>
                </a>
            </li>
            <li class="px-xxl-3 px-2 flex align-items-stretch">
                <div class="relative inline-block text-left custom-dropdown flex items-center py-4">
                    <button class="px-4 py-2 rounded font-medium transition-colors inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hide-arrow ps-2 pe-0 py-0 relative" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell text-primary-600 fs-2"></i>
                            @if($notificationCount > 0)
                                <span class="absolute notification-count top-0 start-100 translate-middle badge badge-circle bg-red-600" id="counter">
                    {{ ($notificationCount)  }}
                    <span class="visually-hidden">{{ __('messages.unread_messages')  }}</span>
                            @endif
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="dropdownMenuButton1">
                        <div class="text-start border-bottom py-4 px-7">
                            <h3 class="text-gray-900 mb-0">{{ __('messages.notification.notifications') }}</h3>
                        </div>
                        <div class="px-7 mt-5 inner-scroll height-270">
                            @if($notificationCount > 0)
                                @foreach($notifications as $notification)
                                    <div class="flex relative mb-5 readNotification cursor-pointer"
                                         data-id="{{ $notification->id  }}" id="readNotification">
                                                            <span class="me-5 text-primary-600 fs-2 icon-label">
                                                                <i class="{{ getNotificationIcon($notification->type)  }}"></i></span>
                                        <div>
                                            <h5 class="text-gray-900 fs-6 mb-2">{{ $notification->title }}</h5>
                                            <h6 class="text-gray-600 fs-small fw-light mb-0">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(null, true) }}</h6>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state fs-6 text-gray-800 fw-bold text-center mt-5" data-height="400">
                                    <p>{{ __('messages.notification.empty_notifications')  }}</p>
                                </div>
                            @endif
                            <div class="empty-state fs-6 text-gray-800 fw-bold text-center mt-5 hidden"
                                 data-height="400">
                                <p>{{ __('messages.notification.empty_notifications')  }}</p>
                            </div>
                        </div>
                        @if($notificationCount > 0)
                            <div class="text-center border-top p-4">
                                <h5 class="text-primary-600 mb-0 fs-5 cursor-pointer"
                                    id="readAllNotification">{{ __('messages.notification.mark_all_as_read')  }}</h5>
                            </div>
                        @endif
                    </div>

                </div>
            </li>

            <li class="px-xxl-3 px-2 flex align-items-stretch">
                <div class="relative inline-block text-left dropdown-transparent flex align-items-stretch">
                    <button class="px-4 py-2 rounded font-medium transition-colors inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 px-0 text-gray-600 flex items-center" type="button"
                            id="dropdownMenuButton1" data-bs-auto-close="outside"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="image image-circle image-mini flex items-center me-sm-3">
                            <img src="{{ getLoggedInUser()->avatar  }}"
                                 class="img-fluid" alt="profile image">
                        </div>
                        {{ \Illuminate\Support\Facades\Auth::user()->full_name }}
{{ --                        <i class="fa-solid fa-angle-down ms-2"></i>-- }}
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-7 pb-4" aria-labelledby="dropdownMenuButton1"
                         data-bs-auto-close="outside">
                        <div class="text-center border-bottom pb-5">
                            <div class="image image-circle image-tiny mb-5">
                                <img src="{{ getLoggedInUser()->avatar  }}" class="img-fluid" alt="profile image">
                            </div>
                            <h3 class="text-gray-900">{{ \Illuminate\Support\Facades\Auth::user()->full_name }}</h3>
                            <h4 class="mb-0 fw-400 fs-6">{{ \Illuminate\Support\Facades\Auth::user()->email }}</h4>
                        </div>
                        <ul class="pt-4">
                            <li>
                                <a href="javascript:void(0)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 editCandidateProfileModal"
                                   data-id="{{ getLoggedInUserId()  }}">
                                     <span class="dropdown-icon me-4 text-gray-600">
                                        <i class="fa-solid fa-user"></i>
                                     </span> {{ __('messages.user.edit_profile')  }}</a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 changePasswordModal"
                                   href="javascript:void(0)"  data-id="{{ getLoggedInUserId()  }}">
                                    <span class="dropdown-icon me-4 text-gray-600">
                                        <i class="fa-solid fa-lock"></i>
                                    </span> {{ (Str::limit(__('messages.user.change_password'),20,'...'))  }}
                                </a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900 changeLanguageModal"
                                   href="javascript:void(0)" data-id="{{ getLoggedInUserId()  }}">
                                    <span class="dropdown-icon me-4 text-gray-600">
                                        <i class="fa-solid fa-globe"></i>
                                    </span> {{ (Str::limit(__('messages.user_language.change_language'),20,'...'))  }}
                                </a>
                            </li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray-900" href="{{ url('logout')  }}"
                                   onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                                    <span class="dropdown-icon me-4 text-gray-600">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </span> {{ __('messages.user.logout')  }}
                                </a>
                                <form id="logout-form" action="{{ url('/logout')  }}" method="POST" class="hidden">
                                    {{ csrf_field()  }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="flex items-center">
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors px-0 horizontal-menubar block d-xl-none text-gray-600">
                    <i class="fa-solid fa-bars fs-1"></i>
                </button>
            </li>
        </ul>
    </div>
</header>
<div class="bg-overlay" id="horizontal-menubar-overly"></div>
