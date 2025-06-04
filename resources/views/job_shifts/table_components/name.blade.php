<a class="job-shift-show- px-4 py-2 rounded font-medium transition-colors cursor-pointer text-decoration-none" data-id="{{ $$row->id }}">
    {{ Str::limit($$row->shift, 50) }}
</a>
