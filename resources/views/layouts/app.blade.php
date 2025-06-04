<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="../">
    <title>@yield('title') | {{ getAppName()  }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token()  }}">
    <link rel="shortcut icon" href="{{ getSettingValue('favicon')  }}" />
    <!--begin::Fonts--><!--end::Fonts-->

    @vite(['resources/js/app.js'])
    
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css')  }}">
    @if (getLoggedInUser()->theme_mode)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-dark.css')  }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.dark.css')  }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.dark.css')  }}">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css')  }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.css')  }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pagination-fix.css')  }}">
    @livewireStyles
    @routes

    <!-- Add Alpine.js CDN -->@livewireScripts<script src="{{ asset('js/third-party.js')  }}"></script>
    <script src="{{ asset('js/pages.js')  }}"></script>

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="overflow-x-hidden">
    <div class="flex flex-col flex-root vh-100">
        <div class="flex flex- flex flex-wrap flex-column-fluid">
            @include('layouts.sidebar')
            <div class="wrapper flex flex-col flex- flex flex-wrap -fluid">
                <div class="container mx-auto px-4 mx-auto -fluid flex align-items-stretch justify-between px-0">
                    @include('layouts.header')
                </div>
                <div class="content flex flex-col flex-column-fluid pt-7">
                    @yield('header_toolbar')
                    <div class="flex flex-wrap flex-column-fluid">
                        @yield('content')
                    </div>
                </div>
                <div class="container mx-auto px-4 mx-auto -fluid">
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
    {{ Illuminate\Support\Facades\Log::info(Config::get('app.locale'))  }}
    {{ Illuminate\Support\Facades\Log::info(getLoggedInUser()->language)  }}
    @include('user_profile.edit_profile_modal')
    @include('user_profile.change_password_modal')

    <!--begin::Javascript-->
    {{ Form::hidden('profile-phone-no', old('region_code') . old('phone'), ['id' => 'profilePhoneNo'])  }}

    <script data-turbo-eval="false">
        (function($) {
            let currentLocale = "{{ Config::get('app.locale')  }}";
            Lang.setLocale(currentLocale);
            $.fn.button = function(action) {
                if (action === 'loading' && this.data('loading-text')) {
                    this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
                }
                if (action === 'reset' && this.data('original-text')) {
                    this.html(this.data('original-text')).prop('disabled', false);
                }
            };
        }(jQuery));
        $(document).ready(function() {
            $('.alert').delay(5000).slideUp(300);
        });
        $('[data-dismiss=modal]').on('click', function(e) {
            var $t = $(this),
                target = $t[0].href || $t.data('target') || $t.parents('.modal') || [];

            $(target).modal('hide');
        });
        let utilsScript = "{{ asset('assets/js/inttel/js/utils.min.js')  }}";
        {{ --    let loggedInUserId = "{{ getLoggedInUserId()  }}"; --}}
        let currentUrlName = "{{ Request::url()  }}";
        let readAllNotifications = "{{ url('admin/read-all-notification')  }}";
        let readNotification = "{{ url('admin/notification')  }}";
        let ajaxCallIsRunning = false;
        let usersRole = '{{ !empty(getLoggedInUser()->roles->first()) ? getLoggedInUser()->roles->first()->name : ''  }}';
        let sweetAlertIcon = "{{ asset('images/remove.png')  }}"
        let getLoggedInUserLang = '{{ getCurrentLanguageCode()  }}';
        let defaultCountryCodeValue = "{{ getSettingValue('default_country_code')  }}"
    </script>
    <!--end::Page Custom Javascript-->
    <!--end::Javascript-->

    <!-- Notification Component -->
    <x-notification />
</body>
<!--end::Body-->

</html>
