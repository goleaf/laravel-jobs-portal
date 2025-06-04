<div class="language-switcher dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        @foreach(config('languages.available', ['en']) as $locale)
            <li>
                <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}" 
                   href="{{ route('language.change', $locale) }}">
                    <span class="flag-icon flag-icon-{{ $locale === 'en' ? 'us' : $locale }}"></span>
                    {{ __("languages.{$locale}") }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}

.flag-icon {
    width: 20px;
    height: 15px;
    margin-right: 8px;
}

[dir="rtl"] .language-switcher {
    direction: rtl;
}
</style>