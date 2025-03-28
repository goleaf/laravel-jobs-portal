@props([
    'method' => 'POST',
    'action' => '',
    'hasFiles' => false,
    'class' => '',
])

@php
    $spoofedMethods = ['PUT', 'PATCH', 'DELETE'];
    $method = strtoupper($method);
    $hasMethodField = in_array($method, $spoofedMethods);
@endphp

<form 
    method="{{ $hasMethodField ? 'POST' : $method }}" 
    action="{{ $action }}"
    @if($hasFiles) enctype="multipart/form-data" @endif
    {{ $attributes->merge(['class' => $class]) }}
>
    @csrf
    
    @if($hasMethodField)
        @method($method)
    @endif
    
    {{ $slot }}
</form> 