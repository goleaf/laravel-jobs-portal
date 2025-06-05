@extends('layouts.app')

@section('title')
    {{ __('dashboard.realtime_dashboard') }} - {{ config('app.name') }}
@endsection

@section('content')
    <x-realtime-dashboard />
@endsection

@section('scripts')
    @parent
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection 