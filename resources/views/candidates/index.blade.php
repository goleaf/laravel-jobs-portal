@extends('layouts.app')
@section('content')
    <h1>{{ __('candidates.candidates') }}</h1>
    <ul>
        @foreach($candidates as $candidate)
            <li>{{ $candidate->user->name ?? __('candidates.no_name') }} ({{ $candidate->user->email ?? __('candidates.no_email') }})</li>
        @endforeach
    </ul>
@endsection 