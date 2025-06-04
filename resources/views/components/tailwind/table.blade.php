@props([
    "striped" => false,
    "hover" => false
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge([
        "class" => "min-w-full divide-y divide-gray-200 " . 
                   ($striped ? "odd:bg-gray-50 even:bg-white " : "") .
                   ($hover ? "hover:bg-gray-50 " : "")
    ])  }}>
        {{ $slot  }}
    </table>
</div>