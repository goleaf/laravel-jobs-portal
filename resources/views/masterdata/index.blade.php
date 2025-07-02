@extends("layouts.app")

@section("title", __('masterdata.master_data_management'))

@section("content")
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('masterdata.master_data_management') }}</h1>
        </div>
        <div class="bg-gray-50 rounded-lg p-6">
            @if(empty($data))
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg mb-2">{{ __('masterdata.no_data_found') }}</div>
                    <p class="text-gray-400">{{ __('masterdata.start_adding_records') }}</p>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg mb-2">{{ __('masterdata.data_available') }}</div>
                    <p class="text-gray-400">{{ __('masterdata.records_count', ['count' => count($data)]) }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
