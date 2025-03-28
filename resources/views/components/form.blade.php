@props([
    'method' => 'POST',
    'action' => '',
    'id' => null,
    'class' => '',
    'files' => false,
    'autocomplete' => 'on'
])

@php
    $method = strtoupper($method);
    $formMethod = in_array($method, ['GET', 'POST']) ? $method : 'POST';
@endphp

{!! Form::open([
    'url' => $action,
    'method' => $formMethod,
    'id' => $id,
    'class' => $class,
    'files' => $files,
    'autocomplete' => $autocomplete,
]) !!}

@if (!in_array($method, ['GET', 'POST']))
    @method($method)
@endif

@csrf

{{ $slot }}

{!! Form::close() !!} 