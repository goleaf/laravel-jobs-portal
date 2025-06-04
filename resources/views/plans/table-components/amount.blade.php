<div class="badge bg-gray-100 -success">
    {{ currencyFormat($row->amount, $row->salaryCurrency?$row->salaryCurrency->currency_code : "INR") }}
</div>
