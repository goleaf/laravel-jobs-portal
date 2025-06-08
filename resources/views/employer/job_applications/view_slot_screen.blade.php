@extends('employer.layouts.app')
@section('title')
    {{ __('messages.job_stage.slots') }}
@endsection
@push('css')@endpush
@section('content')
    @include('flash::message')
        <div class="flex flex- flex-1">
            @include('layouts.errors')
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    <div class="flex justify-end">
                        @php
                            $stageId = null;
                        @endphp
                        @if(isset($lastStage) && $jobStage->isNotEmpty())
                            @php
                                $stageId = $lastStage->stage_id;
                            @endphp
                            <div class="w-1/4">
                                {{ Form::select('stage_id', $jobStage, $lastStage->stage_id, ['id' => 'stages', 'class' => 'form-select status-filter w-100']) }}
                            </div>
                        @endif
                        @if($isSelectedRejectedSlot > 0 || $isStageMatch)
                            <div class="flex items-center me-4 me-md-5 form- px-4 py-2 rounded font-medium transition-colors schedule-interview">
                                <a href="javascript:void(0)"
                                   class="border border-gray-300 bg-transparent">
                                    {{ __('messages.common.add') }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <hr>
                    @livewire('view-slot-screen',['applicationId'=>$applicationId, 'stageId'=>$stageId])
                </div>
            </div>
            @include('employer.job_applications.schedule_interview_modal')
{{-- @include('employer.job_applications.templates.templates') --}}
            @include('employer.job_applications.add_batch_slot_modal')
            @include('employer.job_applications.edit_batch_slot_modal')
        </div>
        {{ Form::hidden('indexEmployerJobSlot',true,['id'=>'indexEmployerJobSlot']) }}
@endsection
@push('scripts')
    {{ -- <script src=" asset('assets/js/job_applications/job_slots.js') "></script> -- }}
@endpush


@push('scripts')
    @vite('resources/js/components/view_slot_screen.js')
@endpush
