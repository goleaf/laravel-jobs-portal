@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
])

<div class="mb-4">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="space-y-2">
        @foreach($options as $value => $optionLabel)
            <x-form-radio
                name="{{ $name }}"
                value="{{ $value }}"
                label="{{ $optionLabel }}"
                :checked="$selected == $value"
                :required="$required"
                :disabled="$disabled"
                :class="$class"
            />
        @endforeach
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 