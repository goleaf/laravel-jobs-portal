@props([
    'name',
    'options' => [],
    'id' => null,
    'label' => null,
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50',
    'containerClass' => 'mb-4',
    'error' => null
])

@php
    $id = $id ?? $name;
    $selected = old($name, $selected);
@endphp

<div class="{{ $containerClass }}">
    @if($label)
        {!! Form::label($name, $label, ['class' => 'block font-medium text-sm text-gray-700']) !!}
    @endif

    {!! Form::select(
        $multiple ? $name . '[]' : $name, 
        $options, 
        $selected, 
        [
            'id' => $id,
            'class' => $errors->has($name) ? $class . ' border-red-500' : $class,
            'placeholder' => $placeholder,
            'required' => $required,
            'disabled' => $disabled,
            'multiple' => $multiple
        ]
    ) !!}

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 