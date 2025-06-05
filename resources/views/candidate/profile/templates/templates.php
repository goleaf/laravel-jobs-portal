<script id="candidateExperienceTemplate" type="text/x-jsrender">
  <div class="overflow-hidden shadow rounded-lg rounded p-5 mb-5 bg-white shadow md:w-full px-4 sm:w-full px-4 w-full px-4 flex-1 px-4 -lg-12 candidate-experience"
  data-experience-id="{{:candidateExperienceNumber}}" data-id="{{:id}}">
    <article class="article article-style-b">
        <div class="article-details">
        <div class="justify-between flex">
            <div class="article-title">
                <h4 class="text-indigo-600">{{:title}}</h2>
                <h6 class="text-gray-500">{{:company}}</h3>
            </div>
            <div class="article-cta candidate-experience-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="rounded border border text-indigo-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 edit-candidate-experience px-2 fs-3 ps-0"
                                           title="Edit"
                                           data-id="{{:id}}"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="rounded border border text-red-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 delete-experience px-2 fs-3 pe-0"
                                           title="Delete"
                                           data-id="{{:id}}"><i class="fa-solid fa-trash"></i></a>
                                    </div>
            </div>
            <span class="text-gray-500">{{:startDateExperience}} - {{:endDateExperience}} | {{:country}}</span>
            <p>{{:description}}</p>
        </div>
    </article>
</div>


</script>

<script id="candidateEducationTemplate" type="text/x-jsrender">
  <div class="overflow-hidden shadow rounded-lg rounded p-5 mb-5 bg-white shadow md:w-full px-4 sm:w-full px-4 w-full px-4 flex-1 px-4 -lg-12 candidate-education" data-education-id="{{:candidateEducationNumber}}" data-id="{{:id}}">
      <article class="article article-style-b">
          <div class="article-details">
          <div class="justify-between flex">
              <div class="article-title">
                  <h4 class="text-indigo-600 education-degree-level">{{:degreeLevel}}</h2>
                  <h6 class="text-gray-500">{{:degreeTitle}}</h4>
              </div>
              <div class="article-cta candidate-education-edit-delete">
                                        <a href="javascript:void(0)"
                                           class="rounded border border text-indigo-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 ps-0 edit-candidate-education"
                                           title="Edit"
                                           data-id="{{:id}}"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)"
                                           class="rounded border border text-red-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 pe-0 delete-education"
                                           title="Delete"
                                           data-id="{{:id}}"><i class="fa-solid fa-trash"></i></a>
                                    </div>
              </div>
              <span class="text-gray-500">{{:year}} | {{:country}}</span>
              <p>{{:institute}}</p>
<!--              <div class="article-cta candidate-education-edit-delete">-->
<!--                  <a href="javascript:void(0)" class="rounded border rounded border border border inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-1/20 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 action- edit-education" title="Edit"-->
<!--                     data-id="{{:id}}"><i class="p-1 fa fa-edit"></i></a>-->
<!--                  <a href="javascript:void(0)" class="rounded border rounded border border border inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 action- delete-education" title="Delete"-->
<!--                     data-id="{{:id}}"><i class="p-1 fa fa-trash"></i></a>-->
<!--              </div>-->
          </div>
      </article>
  </div>



</script>
<script id="CVcandidateExperienceTemplate" type="text/x-jsrender">
  <div class="overflow-hidden shadow rounded-lg rounded p-5 mb-5 bg-white shadow md:w-full px-4 sm:w-full px-4 w-full px-4 flex-1 px-4 -lg-12 candidate-experience"
  data-experience-id="{{:candidateExperienceNumber}}" data-id="{{:id}}">
      <article class="article article-style-b">
          <div class="border article-details -0">
          <div class="justify-between flex">
              <div class="article-title">
                  <h5 class="text-indigo-600 experience-title">{{:title}}</h5>
                  <h6 class="text-gray-500">{{:company}}</h3>
              </div>
              <div class="article-cta candidate-experience-edit-delete">
                        <a href="javascript:void(0)"
                           class="rounded border border text-indigo-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 ps-0 edit-experience"
                           title="Edit"
                           data-id="{{:id}}"> <i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:void(0)"
                           class="rounded border rounded border border border inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 text-red-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 pe-0 -sm delete-experience"
                           title="Delete"
                           data-id="{{:id}}"><i class="fa-solid fa-trash"></i></a>
                    </div>
              </div>
              <span class="text-gray-500">{{:startDate}} - {{:endDate}} | {{:country}}</span>
              <p class="fw-bold">{{:description}}</p>
          </div>
      </article>
  </div>


</script>

<script id="CVcandidateEducationTemplate" type="text/x-jsrender">
  <div class="overflow-hidden shadow rounded-lg rounded p-5 mb-5 bg-white shadow md:w-full px-4 sm:w-full px-4 w-full px-4 flex-1 px-4 -lg-12 candidate-education" data-education-id="{{:candidateEducationNumber}}" data-id="{{:id}}">
        <article class="article article-style-b">
            <div class="border article-details -0">
              <div class="justify-between flex">
                <div class="article-title">
                    <h5 class="text-indigo-600 education-degree-level">{{:degreeLevel}}</h5>
                    <h6 class="text-gray-500">{{:degreeTitle}}</h4>
                </div>
                <div class="article-cta candidate-education-edit-delete">
                        <a href="javascript:void(0)"
                           class="rounded border border text-indigo-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 ps-0 edit-education"
                           title="Edit"
                           data-id="{{:id}}">
                           <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                        <a href="javascript:void(0)"
                           class="rounded border border text-red-600 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 px-2 fs-3 pe-0 delete-education"
                           title="Delete"
                           data-id="{{:id}}"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
                <span class="text-gray-500">{{:year}} | {{:country}}</span>
                <p class="fw-bold">{{:institute}}</p>
            </div>
        </article>
    </div>



</script>

<script id="resumeActionTemplate" type="text/x-jsrender">
 <a href="{{:downloadResume}}" class="rounded border rounded border rounded border rounded border rounded border border border border border border inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 bg-gray-100 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 download-link -icon - -active-flex-1 px-4or-primary -sm"><i class="fas fa-download download-margin"></i></a>
<a title="<?php echo __('messages.common.delete') ?>" data-id="{{:id}}" class="rounded border rounded border rounded border rounded border rounded border border border border border border inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 bg-gray-100 inline-flex items-center px-4 py-2 -transparent text-sm font-medium -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200 delete-resume -icon - -active-flex-1 px-4or-danger -sm">
        <span class="svg-icon svg-icon-3">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24" />
        <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero" />
        <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3" /></g></svg></span>
</a>

</script>
