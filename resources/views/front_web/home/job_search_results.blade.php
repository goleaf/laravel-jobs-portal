{{ --  <ul class="shadow rounded mt-2 bg-white text-left relative inline-block origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 block relative w-full job-search-" role="menu">
    @if(!empty($results))
        @foreach($results as $result)
            <li>{{ $result }}</li>
        @endforeach
    @else
        <li class="language-text">{{ __('messages.no_keyword_found') }}</li>
    @endif
</ul>  --}}
