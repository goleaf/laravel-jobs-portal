{{-- Modern Job Grid with Context7 TailwindCSS Components --}}
<x-job-grid 
    :jobs="$jobs" 
    layout="default" 
    columns="auto"
    :showPagination="true"
    :emptyMessage="__('web.job_menu.no_results_found')"
/>
