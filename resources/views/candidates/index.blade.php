@extends('layouts.app')
@section('content')
    <h1>{{ __('candidates.candidates') }}</h1>
    <ul>
        @foreach($candidates as $candidate)
            <li>{{ $candidate->user->name ?? 'No Name' }} ({{ $candidate->user->email ?? 'No Email' }})</li>
        @endforeach
    </ul>
@endsection 