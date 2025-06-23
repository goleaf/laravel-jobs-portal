document.addEventListener('DOMContentLoaded', function() {
document.addEventListener('DOMContentLoaded', function() {
document.addEventListener('DOMContentLoaded', function() {
document.addEventListener('DOMContentLoaded', function() {
document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
var hostUrl = 'assets/';
        let getLoggedInUserLang = '{{ getCurrentLanguageCode() }}';
        let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}"
        Lang.setLocale(getLoggedInUserLang)


});
// Component-specific JavaScript
let siteKey ="{{ config('app.google_recaptcha_site_key') }}"

// Component-specific JavaScript
let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}"


});
// Component-specific JavaScript
var hostUrl = 'assets/';
    let getLoggedInUserLang = '{{ getCurrentLanguageCode() }}';
    let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}"
    Lang.setLocale(getLoggedInUserLang);

// Component-specific JavaScript
$(document).ready(function () {
        $('.alert').delay(5000).slideUp(300);
    });
    var stripe = '';
    @if(!empty(getEnvSetting()['stripe_key']))
         stripe = Stripe('{{ getEnvSetting()['stripe_key'] }}');
    @elseif(config('services.stripe.key'))
        stripe = Stripe('{{ config('services.stripe.key') }}');
    @endif

    //fix menu overflow under the responsive table
    // hide menu on click... (This is a must because when we open a menu )
    $(document).click(function (event) {
        //hide all our dropdowns
        $('.dropdown-menu[data-parent]').hide();
    });

    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css("overflow","unset");
    }).on('hide.bs.dropdown', function () {
        $('.table-responsive').css("overflow","auto");
    })


});
// Component-specific JavaScript
let siteKey ="{{ config('app.google_recaptcha_site_key') }}"

// Component-specific JavaScript
let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}";
        let currentFrontLang ="{{ session()->get('languageName') ?? 'en' }}";
        Lang.setLocale(currentFrontLang);


});
// Component-specific JavaScript
(function($) {
            let currentLocale ="{{ Config::get('app.locale') }}";
            Lang.setLocale(currentLocale);
            $.fn.button = function(action) {
                if (action === 'loading' && this.data('loading-text')) {
                    this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
                }
                if (action === 'reset' && this.data('original-text')) {
                    this.html(this.data('original-text')).prop('disabled', false);
                }
            };
        }(jQuery));
        $(document).ready(function() {
            $('.alert').delay(5000).slideUp(300);
        });
        $('[data-dismiss=modal]').on('click', function(e) {
            var $t = $(this),
                target = $t[0].href || $t.data('target') || $t.parents('.modal') || [];

            $(target).modal('hide');
        });
        let utilsScript ="{{ asset('assets/js/inttel/js/utils.min.js') }}";
        {{-- let loggedInUserId ="{{ getLoggedInUserId() }}"; --}}
        let currentUrlName ="{{ Request::url() }}";
        let readAllNotifications ="{{ url('admin/read-all-notification') }}";
        let readNotification ="{{ url('admin/notification') }}";
        let ajaxCallIsRunning = false;
        let usersRole = '{{ !empty(getLoggedInUser()->roles->first()) ? getLoggedInUser()->roles->first()->name : '' }}';
        let sweetAlertIcon ="{{ asset('images/remove.png') }}"
        let getLoggedInUserLang = '{{ getCurrentLanguageCode() }}';
        let defaultCountryCodeValue ="{{ getSettingValue('default_country_code') }}"


});