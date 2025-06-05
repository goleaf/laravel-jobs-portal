@extends('candidate.profile.index')
@section('section')
<div class="flex-1 px-4 flex flex-">
    <livewire:resume-min-w-full divide-y divide-gray-200/>
    @include('candidate.profile.modals.upload_resume_modal')
</div>

@endsection
