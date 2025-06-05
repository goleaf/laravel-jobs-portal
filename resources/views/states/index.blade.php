@extends('layouts.app')
@section('title')
    {{ __('messages.state.states') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="flex flex-col">
            <livewire:state-table/>
        </div>
    </div>
    @include('states.add_modal')
    @include('states.edit_modal')
    {{ Form::hidden('stateData',true,['id'=>'indexStateData']) }}
@endsection
{{ --@push('scripts')-- }}
    {{ --    <script src="{{mix('assets/js/states/states.js') }}"></script>--}}
{{ --@endpush-- }}
