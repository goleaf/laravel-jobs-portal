@extends('layouts.app')
@section('content')
    <h1>Candidate Details</h1>
    <p>Name: {{ $candidate->user->name ?? 'No Name' }}</p>
    <p>Email: {{ $candidate->user->email ?? 'No Email' }}</p>
    <p>Experience: {{ $candidate->experience }}</p>
@endsection 