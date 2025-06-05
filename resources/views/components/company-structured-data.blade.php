@props(['company'])

@php
    $structuredData = \App\Services\SEOService::generateOrganizationStructuredData($company);
@endphp


@push('scripts')
    @vite('resources/js/components/company-structured-data.js')
@endpush
