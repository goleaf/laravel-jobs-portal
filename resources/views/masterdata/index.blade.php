@extends("layouts.app")

@section("title", "Master Data Management")

@section("content")
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Master Data Management</h1>
        </div>
        <div class="bg-gray-50 rounded-lg p-6">
            @if(empty($data))
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg mb-2">No master data found</div>
                    <p class="text-gray-400">Start by adding your first master data record</p>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg mb-2">Master Data Available</div>
                    <p class="text-gray-400">{{ count($data) }} records found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
