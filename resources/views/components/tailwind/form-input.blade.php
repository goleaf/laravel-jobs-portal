@props([
    "label" => null,
    "error" => null,
    "help" => null,
    "type" => "text",
    "required" => false
])

<div>
    @if($label)
        <label {{ $attributes->only("id")->mapWithKeys(fn($value, $key) => ["for" => $value]) }} class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        {{ $attributes->merge([
            "class" => "w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 " . ($error ? "border-red-300" : "border-gray-300")
        ]) }}
    >
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @elseif($help)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>