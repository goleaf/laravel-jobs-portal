@extends('layouts.app')
@section('title')
    {{ __('messages.post.comment') }}
@endsection
@section('content')
<div class="container mx-auto -fluid">
    <div class="flex flex-column">
        @include('flash::message')
        <livewire:post-comment-table />
        {{Form::hidden('userCurrentLanguage',getCurrentLanguageCode(),['id'=>'postCommentLanguage'])}}
    </div>
</div>
@include('post_comments.show_model')
@endsection
@push('scripts')
{{--    <script src="{{mix('assets/js/post_comments/post_comments.js')}}"></script>--}}
@endpush
