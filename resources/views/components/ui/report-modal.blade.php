@props([
  'id' => 'report-modal',
  'type' => 'job',
  'title' => '',
  'description' => '',
  'endpoint' => route('reports.store'),
  'idField' => 'id',
  'idValue' => null,
])

<x-ui.modal :id="$id" :title="__('ui.report')" size="md">
  <form method="POST" action="{{ $endpoint }}" class="space-y-4">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}" />
    @if($idValue)
      <input type="hidden" name="{{ $idField }}" value="{{ $idValue }}" />
    @endif

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.reason') }}</label>
      <select name="reason" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
        <option value="spam">{{ __('ui.reason_spam') }}</option>
        <option value="inaccurate">{{ __('ui.reason_inaccurate') }}</option>
        <option value="abusive">{{ __('ui.reason_abusive') }}</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.details') }}</label>
      <textarea name="details" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
    </div>

    <div class="flex justify-end gap-3">
      <button type="button" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" data-modal-close="{{ $id }}">{{ __('ui.cancel') }}</button>
      <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">{{ __('ui.submit_report') }}</button>
    </div>
  </form>
</x-ui.modal>
