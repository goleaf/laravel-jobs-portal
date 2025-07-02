@extends('layouts.app')

@section('title', __('jobs.create_new_job'))

@section('content')
<div class="container">
    <h1>{{ __('jobs.create_new_job') }}</h1>
    <p>{{ __('jobs.creation_form_placeholder') }}</p>
</div>
@endsection 