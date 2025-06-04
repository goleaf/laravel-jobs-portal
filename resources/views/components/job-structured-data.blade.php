@props(['job'])

@php
    $structuredData = \App\Services\SEOService::generateJobStructuredData($job);
@endphp

<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>