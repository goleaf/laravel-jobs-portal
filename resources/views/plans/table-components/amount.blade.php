<div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-gray-100 success">
    {{ currencyFormat($flex flex-wrap -mx-4->amount, $flex flex-wrap -mx-4->salaryCurrency?$flex flex-wrap -mx-4->salaryCurrency->currency_code :"INR") }}
</div>
