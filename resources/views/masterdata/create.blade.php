@extends("layouts.app") 

@section("title", __('masterdata.create_master_data')) 

@section("content") 
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('masterdata.create_master_data') }}</h1>
            <a href="{{ route('masterdata.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">{{ __('masterdata.back') }}</a>
        </div>
        <p class="text-gray-600">{{ __('masterdata.create_form_placeholder') }}</p>
    </div>
</div>
@endsection
