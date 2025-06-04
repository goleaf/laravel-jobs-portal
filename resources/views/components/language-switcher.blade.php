@php
    $languages = [
        'en' => 'EN',
        'lt' => 'LT'
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="language-switcher relative inline-block text-left">
    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        {{ strtoupper($currentLocale) }}
    </button>
    <ul class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" aria-labelledby="languageDropdown">
        @foreach($languages as $code => $name)
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === $code ? "active' : '' }}" 
                   href="{{ route('language.switch', $code) }}">
                    <span class="flag-icon flag-icon-{{ $code === 'en' ? 'us' : $code }}"></span>
                    {{ $name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher .flag-icon {
    margin-right: 0.5rem;
}
.language-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}
</style>