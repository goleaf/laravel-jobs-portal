@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.candidate_details') }}
@endsection
@section('header_toolbar')
    <div class="container mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('admin.candidates.edit',$candidate->id) }}"
                   class="btn px-4 py-2 rounded font-medium transition-colors -primary me-4">{{ __('messages.common.edit') }}</a>
                <a href="{{ route('admin.candidates.index') }}" class="btn px-4 py-2 rounded font-medium transition-colors -outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container mx-auto -fluid">
        <div class="flex flex-column">
            <div class="flex flex-wrap">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    @include('candidates.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
