{{-- Modern Job Grid with Universal TailwindCSS Components --}}
<x-job-grid 
    :jobs="$jobs" 
    layout="default" 
    columns="auto"
    :showPagination="true"
    :emptyMessage="__('web.job_menu.no_results_found')"
/>
