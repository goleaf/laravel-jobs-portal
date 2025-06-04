@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 -md-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-white shadow rounded-lg overflow-hidden -header">{{ __('message.candidate.dashboard') }}</div>

                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        @if (session('status'))
                            <div class="alert p-4 rounded-md mb-4 -success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('web.home_menu.you_are_logged_in') }}
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
