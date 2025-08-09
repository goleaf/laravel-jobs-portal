<p>Hi {{ $candidate_name ?? '' }},</p>
<p>We received your application for {{ $job_title ?? 'the job' }} at {{ $company_name ?? '' }}.</p>
<p>Date: {{ $application_date ?? '' }}</p>
<p>Status: {{ $application_status ?? '' }}</p>
<p><a href="{{ $application_tracking_url ?? '#' }}">Track your application</a></p>
