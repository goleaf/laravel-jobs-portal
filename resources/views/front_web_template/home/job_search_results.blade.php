{{  --  <ul class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 block relative w-full job-search-dropdown" role="menu">
    @if(!empty($results))
        @foreach($results as $result)
            <li> $result </li>
        @endforeach
    @else
        <li class="language-text">{{ __('messages.no_keyword_found')  }}</li>
    @endif
</ul>  --}}
