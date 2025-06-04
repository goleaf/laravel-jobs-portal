<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-employee position-relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-column">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $testimonial->customer_image_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2 image-stretching">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full mb-2">
                        <div>
                            <span class="text-decoration-none text-color-gray one-line-ellip">
                                 <a href="javascript:void(0)" class="show- px-4 py-2 rounded font-medium transition-colors"
                                    data-id="{{$testimonial->id}}">{{ Str::limit($testimonial->customer_name, 40, '...') }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="download-resume">
                <a href="{{ route('download.image', $testimonial->id) }}"
                   class="download-link"><i class="fas fa-download"></i> {{ __('messages.common.download') }}</a>
            </div>
        </div>

        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit') }}" class="btn bg-yellow-500 text-white hover:bg-yellow-600 action-btn edit- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$testimonial->id}}" href="#">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete') }}" class="btn bg-red-600 text-white hover:bg-red-700 action-btn delete- px-4 py-2 rounded font-medium transition-colors"
               data-id="{{$testimonial->id}}" href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
