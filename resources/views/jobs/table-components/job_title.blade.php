<a class="cursor-pointer text-decoration-none" href="{{ route('admin.', $row->id) }}">
    {{ Str::limit($row->job_title,35) }}
</a>
