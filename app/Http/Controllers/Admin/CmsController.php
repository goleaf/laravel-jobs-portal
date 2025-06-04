<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AppBaseController;
use App\Models\Testimonial;
use App\Models\BrandingSlider;
use App\Models\HeaderSlider;
use App\Models\ImageSlider;
use App\Models\CmsServices;
use App\Models\Setting;
use Illuminate\View\View;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;

class CmsController extends AppBaseController
{
    /**
     * Display testimonials listing
     */
    public function testimonials(): View
    {
        return view('testimonial.index');
    }
    
    /**
     * Display FAQs listing
     */
    public function faqs(): View
    {
        $faqs = []; // Replace with actual model data
        return view('admin.faqs.index', compact('faqs'));
    }
    
    /**
     * Display noticeboards listing
     */
    public function noticeboards(): View
    {
        $noticeboards = []; // Replace with actual model data
        return view('admin.noticeboards.index', compact('noticeboards'));
    }
    
    /**
     * Display post categories listing
     */
    public function postCategories(): View
    {
        $postCategories = []; // Replace with actual model data
        return view('admin.post_categories.index', compact('postCategories'));
    }
    
    /**
     * Display posts listing
     */
    public function posts(): View
    {
        $posts = []; // Replace with actual model data
        return view('admin.posts.index', compact('posts'));
    }
    
    /**
     * Show form to create new post
     */
    public function createPost(): View
    {
        return view('admin.posts.create');
    }
    
    /**
     * Show specific post
     */
    public function showPost(string $post): View
    {
        // Replace with actual model lookup
        $postData = (object)['id' => $post, 'title' => 'Sample Post'];
        return view('admin.posts.show', compact('postData'));
    }
    
    /**
     * Show form to edit post
     */
    public function editPost(string $post): View
    {
        // Replace with actual model lookup
        $postData = (object)['id' => $post, 'title' => 'Sample Post'];
        return view('admin.posts.edit', compact('postData'));
    }
    
    /**
     * Display post comments listing
     */
    public function postComments(): View
    {
        $postComments = []; // Replace with actual model data
        return view('admin.post_comments.index', compact('postComments'));
    }
    
    /**
     * Display branding sliders listing
     */
    public function brandingSliders(): View
    {
        return view('branding_sliders.index');
    }
    
    /**
     * Display header sliders listing
     */
    public function headerSliders(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('header_sliders.index', compact('settings'));
    }
    
    /**
     * Display image sliders listing
     */
    public function imageSliders(): View
    {
        return view('image_sliders.index');
    }
    
    /**
     * Display CMS services listing
     */
    public function cmsServices(): View
    {
        return view('cms_services.index');
    }
    
    /**
     * Display CMS about us page
     */
    public function cmsAboutUs(): View
    {
        $aboutUsServices = Setting::where('key', 'about_us_services')->first();
        return view('cms_services.about_us', compact('aboutUsServices'));
    }
} 