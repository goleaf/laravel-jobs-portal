<script id="companyActionTemplate" type="text/x-jsrender">
   <a title="<?php echo __('messages.common.edit'); ?>" class="btn bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500 action- inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out edit- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out" href="{{:url}}">
            <i class="fa fa-edit"></i>
   </a>
   <a title="<?php echo __('messages.common.delete'); ?>" class="btn bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 action- inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out delete- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out" data-id="{{:id}}" href="#">
            <i class="fa fa-trash"></i>
   </a>  

</script>

<script id="isFeatured" type="text/x-jsrender">
   <label class="custom-switch pl-0">
        <input type="checkbox" name="Is Featured" class="custom-switch-input isFeatured" data-id="{{:id}}" {{:checked}}>
        <span class="custom-switch-indicator"></span>
    </label>

</script>

<script id="isActive" type="text/x-jsrender">
   <label class="custom-switch pl-0">
        <input type="checkbox" name="Is Active" class="custom-switch-input isActive" data-id="{{:id}}" {{:checked}}>
        <span class="custom-switch-indicator"></span>
    </label>


</script>

<script id="reportedCompanyActionTemplate" type="text/x-jsrender">
<a title="<?php echo __('messages.common.delete'); ?>" data-id={{:id}}" class="delete-btn btn btn-icon btn- bg-gray-100 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out -active-color-danger inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out -sm">
        <span class="svg-icon svg-icon-3">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24" />
        <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero" />
        <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3" /></g></svg></span>
</a>



</script>
