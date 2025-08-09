@props([
  'variant' => 'primary',
  'size' => 'md',
])

@php
$base = 'inline-flex items-center rounded-full font-medium';
$sizes = [
  'sm' => 'px-2.5 py-0.5 text-xs',
  'md' => 'px-3 py-1 text-sm',
  'lg' => 'px-3.5 py-1.5 text-sm',
];
$variants = [
  'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200',
  'secondary' => 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:text-secondary-200',
  'success' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200',
  'warning' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
  'error' => 'bg-error-100 text-error-800 dark:bg-error-900 dark:text-error-200',
  'outline' => 'border border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200 bg-transparent',
];
@endphp

<span {{ $attributes->merge(['class' => trim("$base ".($sizes[$size] ?? $sizes['md'])." ".($variants[$variant] ?? $variants['primary']))]) }}>
  {{ $slot }}
</span>
