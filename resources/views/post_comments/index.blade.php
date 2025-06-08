@extends('layouts.app')
@section('title')
    {{ __('messages.post.comment') }}
@endsection
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
    <div class="flex flex- flex-1">
        @include('flash::message')
        <livewire:post-comment-table />
        {{ Form::hidden('userCurrentLanguage',getCurrentLanguageCode(),['id'=>'postCommentLanguage']) }}
    </div>
</div>
@include('post_comments.show_model')
@endsection
@push('scripts')
{{ -- <script src="mix('assets/js/post_comments/post_comments.js') "></script> -- }}
@endpush
