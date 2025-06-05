@extends('layouts.app')
@section('title')
    {{ __('messages.post_category.post_categories') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            @include('flash::message')
            <livewire:post-category-table/>
        </div>
    </div>
    @include('blog_categories.add_modal')
    @include('blog_categories.edit_modal')
    @include('blog_categories.show_modal')
@endsection
