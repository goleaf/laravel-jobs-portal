<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * Class PrivacyPolicyController
 */
class PrivacyPolicyController extends AppBaseController
{
    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function showPrivacyPolicy(): View
    {
        $privacyPolicy = Setting::where('key', 'privacy_policy')->value('value');

        return view('front_web_template.privacy_policy.index', compact('privacyPolicy'));
    }

    public function showTermsConditions(): View
    {
        $termsConditions = Setting::where('key', 'terms_conditions')->value('value');

        return view('front_web_template.terms_conditions.index', compact('termsConditions'));
    }
}
