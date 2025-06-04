@extends('front_web.layouts.app')
@section('title')
    {{ __('messages.setting.terms_conditions')  }}
@endsection
@section('content')
    <section class="hero-section relative bg-gray-100 py-40">
        <div class="container mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 -lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                    <div class="hero-content">
                        <h1 class="text-gray-600 mb-3">
                            {{ __('messages.setting.terms_conditions')  }}
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-center mb-0">
                                <li class="breadcrumb-item "><a href="{{ route('front.home')  }}" class="fs-18 text-gray">{{ __('web.home')  }}</a>
                                </li>
                                <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">{{ __('messages.setting.terms_conditions')  }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
<div class="py-60">
    <div class="container mx-auto px-4 mx-auto">
        <div class="mb-40">
            <div class="terms-desc">
                {!! nl2br($termsConditions) !!}
            </div>
        </div>
    </div>
</div>
@endsection

{{ --@section('scripts')-- }}
{{ --    <script>-- }}
{{ --        let registerSaveUrl = "{{ route('front.save.register')  }}";--}}
{{ --        let logInUrl = "{{ route('login')  }}";--}}
{{ --    </script>-- }}
{{ --    <script src="{{asset('assets/js/front_register/front_register.js') }}"></script>--}}
{{ --@endsection-- }}
