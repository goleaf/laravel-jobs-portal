@props([
    'name',
    'id' => null,
    'label' => null,
    'value',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'class' => 'rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50',
    'containerClass' => 'mb-4 flex items-center',
    'error' => null
])

@php
    $id = $id ?? ($name . '_' . $value);
    $checked = old($name) == $value ? true : $checked;
@endphp

<div class="{{ $containerClass }}">
    {!! Form::radio($name, $value, $checked, [
        'id' => $id,
        'class' => $errors->has($name) ? $class . ' border-red-500' : $class,
        'required' => $required,
        'disabled' => $disabled
    ]) !!}

    @if($label)
        {!! Form::label($id, $label, ['class' => 'ml-2 block text-sm text-gray-700']) !!}
    @endif

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 