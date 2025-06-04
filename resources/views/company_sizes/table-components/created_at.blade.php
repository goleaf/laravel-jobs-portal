<div class="badge bg-gray-600">
    {{ \Carbon\Carbon::parse($$row->created_at)->format('jS M, Y')  }}
</div>
