@extends('front_web.layouts.app')
@section('title')
    {{ __('web.job_seekers') }}
@endsection
@section('page_css')
    @if(\Illuminate\Support\Facades\App::getLocale() == 'ar')
        <style>
            .candidate-main ul.pagination {
                direction: rtl;
            }
        </style>
    @endif
{{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/jobs.css') }}">--}}
{{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/companies.css') }}">--}}
@endsection
@section('content')
    <div class="job-seekers-page">
        <section class="hero-section relative bg-color-light py-40">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                @lang('web.job_seekers')
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }} </a>
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">@lang('web.job_seekers')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="latest-job-section py-60">
            @livewire('candidate-search')
        </section>
    </div>
@endsection
{{ --@section('scripts')-- }}
{{ --    <script>-- }}
{{ --        $(document).ready(function () {-- }}
{{ --            $(document).on('click', '#btnReset', function () {-- }}
{{ --                $('#All').prop('checked', true);-- }}
{{ --                $('#searchBy').prop('selectedIndex', 0);-- }}
{{--            });--}}
{{ --            window.livewire.hook('message.processed', () => {-- }}
{{ --                $(window).scrollTop(0);-- }}
{{--            });--}}
{{ --            $('#searchByCandidate, .search-by-location').val('');-- }}
{{--        });--}}
{{ --    </script>-- }}
{{ --@endsection-- }}
