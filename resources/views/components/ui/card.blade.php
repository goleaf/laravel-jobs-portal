@props(['as' => 'div'])

@php
$tag = in_array($as, ['div','section','article']) ? $as : 'div';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700']) }}>
  {{ $slot }}
</{{ $tag }}>
