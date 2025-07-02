@extends('layouts.app')
@section('content')
    <h1>{{ __('candidates.edit_candidate') }}</h1>
    <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}">
        @csrf
        @method('PUT')
        <label>{{ __('candidates.name') }}: <input type="text" name="name" value="{{ $candidate->user->name ?? '' }}"></label><br>
        <label>{{ __('candidates.email') }}: <input type="email" name="email" value="{{ $candidate->user->email ?? '' }}"></label><br>
        <label>{{ __('candidates.experience') }}: <input type="number" name="experience" value="{{ $candidate->experience }}"></label><br>
        <button type="submit">{{ __('candidates.update') }}</button>
    </form>
@endsection 