<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="{{ getAppName() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>404 Not Found | {{ getAppName() }}</title></head>
<body>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto con-404 vh-100 flex justify-center">
    <div class="flex flex-wrap justify-content-md-center block">
        <div class="flex-1 md-12 mt-5">
            <img src="{{ asset('assets/img/404-error-image.png') }}" class="img-fluid img-404 mx-auto block">
        </div>
        <div class="flex-1 md-12 text-center error-page-404">
            <h2>{{ __('messages.something_missing') }}</h2>
            <p class="not-found-subtitle">{{ __('messages.page_not_found') }}.</p>
            <a class="border border-gray-300 bg-transparent" data-turbo="false" href="{{ url()->previous() }}" >{{ __('messages.go_back') }}</a>
        </div>
    </div>
</div></body>
</html>

