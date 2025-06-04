@extends('candidate.profile.index')
@section('section')
<div class="flex flex-col">
    <livewire:resume-table/>
    @include('candidate.profile.modals.upload_resume_modal')
</div>

@endsection
