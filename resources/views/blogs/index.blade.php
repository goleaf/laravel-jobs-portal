@extends('layouts.app')
@section('title')
    {{ __('messages.post.posts')  }}
@endsection
@push('css')
{{ --    <link rel="stylesheet" href="{{ asset('css/header-padding.css')  }}">--}}
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:post-table />
        </div>
    </div>
@endsection
