@extends("layouts.app") 

@section("title", __('masterdata.show_master_data')) 

@section("content") 
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('masterdata.master_data_details') }}</h1>
        <p class="text-gray-600">{{ __('masterdata.details_for_id', ['id' => $id]) }}</p>
    </div>
</div>
@endsection
