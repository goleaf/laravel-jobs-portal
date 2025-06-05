@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.candidate_details') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="mb-5 md:flex items-center justify-between">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="mt-4 text-end mt-md-0">
                <a href="{{ route('admin.',$candidate->id) }}"
                   class="border border-gray-300 bg-transparent">{{ __('messages.common.edit') }}</a>
                <a href="{{ route('admin.candidates.index') }}" class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            <div class="flex-wrap flex">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="overflow-hidden shadow rounded bg-white -lg">
                <div class="overflow-hidden shadow rounded bg-white -lg body">
                    @include('candidates.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
