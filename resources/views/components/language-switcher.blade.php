
@push('styles')
    @vite('resources/css/components/language-switcher.css')
@endpush
@php
    $languages = [
        'en' => 'EN',
        'lt' => 'LT'
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="language-switcher relative inline-block text-left">
    <button class="rounded-md transition" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        {{ strtoupper($currentLocale) }}
    </button>
    <ul class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" aria-labelledby="languageDropdown">
        @foreach($languages as $code => $name)
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === $code ?"active' : '' }}" 
                   href="{{ route('language.switch.index', $code) }}">
                    <span class="flag-icon flag-icon-{{ $code === 'en' ? 'us' : $code }}"></span>
                    {{ $name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

