@extends('layouts.app')
@section('content')
    <h1>Edit Candidate</h1>
    <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}">
        @csrf
        @method('PUT')
        <label>Name: <input type="text" name="name" value="{{ $candidate->user->name ?? '' }}"></label><br>
        <label>Email: <input type="email" name="email" value="{{ $candidate->user->email ?? '' }}"></label><br>
        <label>Experience: <input type="number" name="experience" value="{{ $candidate->experience }}"></label><br>
        <button type="submit">Update</button>
    </form>
@endsection 