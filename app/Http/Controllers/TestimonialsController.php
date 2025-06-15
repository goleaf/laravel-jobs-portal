<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Repositories\TestimonialRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TestimonialsController extends AppBaseController
{
    /**
     * @var TestimonialRepository
     */
    private $testimonialRepository;

    public function __construct(TestimonialRepository $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * @return Factory|View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        return view('testimonial.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTestimonialRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->testimonialRepository->store($input);

        return $this->sendSuccess(__('messages.flash.testimonial_save'));
        //        return $this->sendSuccess('This functionality not allowed in demo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial): JsonResponse
    {
        return $this->sendResponse($testimonial, __('messages.flash.testimonial_retrieve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial): JsonResponse
    {
        return $this->sendResponse($testimonial, __('messages.flash.testimonial_retrieve'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestimonialUpdateTestimonialsRequest $request, Testimonial $testimonial)
    {
        $input = $request->all();
        $this->testimonialRepository->updateTestimonial($input, $testimonial->id);

        return $this->sendSuccess(__('messages.flash.testimonial_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return $this->sendSuccess(__('messages.flash.testimonial_delete'));
    }

    public function downloadImage(Testimonial $testimonial): Media
    {
        $media = $testimonial->getMedia('testimonials')->first()->id;

        /** @var Media $mediaItem */
        return Media::findOrFail($media);
    }
}
