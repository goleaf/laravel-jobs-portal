@extends('layouts.app')
@section('title')
    {{ __('messages.post.edit_post') }}
@endsection
@section('header_toolbar')
    <div class="container mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('posts.index') }}" class="btn px-4 py-2 rounded font-medium transition-colors -outline-primary">{{ __('messages.common.back') }}</a>
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
                    {{ Form::model($post, ['route' => ['posts.update', $post->id], 'method' => 'put', 'id' => 'editBlogForm', 'files' => 'true']) }}

                    @include('blogs.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let blogDescription = '{{$post->description}}';
    </script>
{{--    <script src="{{mix('assets/js/blogs/create-edit.js')}}"></script>--}}
@endpush

