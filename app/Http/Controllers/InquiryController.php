<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class InquiryController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(): View
    {
        return view('inquires.index');
    }

    public function show(Inquiry $inquiry): JsonResponse
    {
        //        return view('inquires.show', compact('inquiry'));

        return $this->sendResponse($inquiry, __('messages.flash.inquiry_retrieve'));
    }

    /**
     * Remove the specified Inquiry from storage.
     *
     * @throws \Exception
     */
    public function destroy(Inquiry $inquiry): JsonResponse
    {
        $inquiry->delete();

        return $this->sendSuccess(__('messages.flash.inquiry_deleted'));
    }
}
