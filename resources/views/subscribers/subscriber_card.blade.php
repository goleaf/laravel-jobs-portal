<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-border position-relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-column">
                <div class="mb-2 text-center">
                    <i class="fas fa-envelope-square subscribers-envelope"></i>
                </div>
                <div class="mb-auto w-full text-center">
                    <label class="text-decoration-none text-color-gray">{{ $subscriber->email }}</label>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.delete') }}" class="btn bg-red-600 text-white hover:bg-red-700 action-btn delete- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$subscriber->id}}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
