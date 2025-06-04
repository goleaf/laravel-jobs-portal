<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-border position-relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-column">
                <div class="mb-auto w-full employee-data mt-4">
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.salary_currency.currency_name') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $salaryCurrency->currency_name }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.salary_currency.currency_code') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{ $salaryCurrency->currency_code }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.salary_currency.currency_icon') }} :</label>
                        <label class="text-decoration-none text-color-gray">{{$salaryCurrency->currency_icon}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
