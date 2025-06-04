
    <div class="badge bg-gray-600">
        {{ \Carbon\Carbon::parse($$row->created_at)->translatedFormat('jS M, Y')  }}
    </div>
