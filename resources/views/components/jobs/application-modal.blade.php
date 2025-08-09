@props(['job'])

<x-ui.modal id="job-application-modal" :title="__('jobs.apply_to', ['title' => $job->title])" size="lg">
  <form method="POST" action="{{ route('jobs.apply', $job) }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('jobs.cover_letter') }}</label>
      <textarea name="cover_letter" rows="6" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required></textarea>
    </div>

    <div class="flex justify-end gap-3">
      <button type="button" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" data-modal-close="job-application-modal">{{ __('ui.cancel') }}</button>
      <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-primary-600 text-white hover:bg-primary-700">{{ __('jobs.submit_application') }}</button>
    </div>
  </form>
</x-ui.modal>
