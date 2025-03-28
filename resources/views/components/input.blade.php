@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'autofocus' => false,
    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50',
    'containerClass' => 'mb-4',
    'error' => null
])

@php
    $id = $id ?? $name;
    $value = old($name, $value);
@endphp

<div class="{{ $containerClass }}">
    @if($label)
        {!! Form::label($name, $label, ['class' => 'block font-medium text-sm text-gray-700']) !!}
    @endif

    @if($type === 'textarea')
        {!! Form::textarea($name, $value, [
            'id' => $id,
            'class' => $errors->has($name) ? $class . ' border-red-500' : $class,
            'placeholder' => $placeholder,
            'required' => $required,
            'disabled' => $disabled,
            'autofocus' => $autofocus
        ]) !!}
    @else
        {!! Form::$type($name, $value, [
            'id' => $id,
            'class' => $errors->has($name) ? $class . ' border-red-500' : $class,
            'placeholder' => $placeholder,
            'required' => $required,
            'disabled' => $disabled,
            'autofocus' => $autofocus
        ]) !!}
    @endif

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div> 