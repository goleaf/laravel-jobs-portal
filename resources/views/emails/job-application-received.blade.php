<p>New application received for: {{ $job_title ?? 'Job' }}</p>
<p>Candidate: {{ $candidate_name ?? '' }} ({{ $candidate_email ?? '' }})</p>
<p>Date: {{ $application_date ?? '' }}</p>
<p><a href="{{ $application_url ?? '#' }}">View Application</a></p>
