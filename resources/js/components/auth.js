document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}";
    let currentFrontLang ="{{ session()->get('languageName') ?? 'en' }}";

// Component-specific JavaScript
$(document).ready(function () {
        $('.alert').delay(5000).slideUp(300);
        $('#loginBtn').click(function () {
            $(this).addClass('disabled')
        })
    })


});