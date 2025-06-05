@props(['job'])

@php
    $structuredData = \App\Services\SEOService::generateJobStructuredData($job);
@endphp


@push('scripts')
    @vite('resources/js/components/job-structured-data.js')
@endpush
