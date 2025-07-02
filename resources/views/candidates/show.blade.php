@extends('layouts.app')
@section('content')
    <h1>{{ __('candidates.candidate_details') }}</h1>
    <p>{{ __('candidates.name') }}: {{ $candidate->user->name ?? __('candidates.no_name') }}</p>
    <p>{{ __('candidates.email') }}: {{ $candidate->user->email ?? __('candidates.no_email') }}</p>
    <p>{{ __('candidates.experience') }}: {{ $candidate->experience }}</p>
@endsection 