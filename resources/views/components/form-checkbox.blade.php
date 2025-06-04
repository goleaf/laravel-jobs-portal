@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
])

@php
    $checkboxId = $id ?? $name;
@endphp

<div class="mb-4 flex items-start">
    <div class="flex items-center h-5">
        <input
            type="checkbox"
            name="{{ $name  }}"
            id="{{ $checkboxId  }}"
            value="{{ $value  }}"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded ' . $class])  }}
        />
    </div>
    
    @if($label)
        <div class="ml-3 text-sm">
            <label for="{{ $checkboxId  }}" class="font-medium text-gray-700">
                {{ $label  }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        </div>
    @endif
</div>

@if($error)
    <p class="mt-1 text-sm text-red-600">{{ $error  }}</p>
@endif 