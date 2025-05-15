@extends('layouts.app')
@section('title')
    {{ __('messages.career_levels') }}
@endsection
@push('css')
{{--<link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">--}}
@endpush
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column ">
            @include('flash::message')
            <livewire:career-level-table/>
        </div>
    </div>
    @include('career_levels.add_modal')
    @include('career_levels.edit_modal')
    {{Form::hidden('careerLevelData',true,['id'=>'indexCareerLevelData'])}}
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listening for the event to fill form when editing
        Livewire.on('fillCareerLevelForm', data => {
            document.getElementById('careerLevelId').value = data.id;
            document.getElementById('editCareerLevel').value = data.levelName;
        });
        
        // Success toast message
        Livewire.on('showSuccessToast', ({message}) => {
            displaySuccessMessage(message);
        });
        
        // Error toast message
        Livewire.on('showErrorToast', ({message}) => {
            displayErrorMessage(message);
        });
    });
</script>
@endpush
