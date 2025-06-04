@php
    $languages = [
        'en' => 'EN',
        'lt' => 'LT'
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="language-switcher dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        {{ strtoupper($currentLocale) }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        @foreach($languages as $code => $name)
            <li>
                <a class="dropdown-item {{ $currentLocale === $code ? 'active' : '' }}" 
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