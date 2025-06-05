@if(App::environment('production'))
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZD1E6560CV"></script>

@endif

@push('scripts')
    @vite('resources/js/components/google_analytics.js')
@endpush
