<?php

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Settings\IndexSettingsRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\EnvSetting;
use App\Models\Language;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Brotzka\DotenvEditor\Exceptions\DotEnvException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

/**
 * Class SettingController.
 */
class SettingController extends AppBaseController
{
    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(IndexSettingsRequest $request): View
    {
        $envData = $this->settingRepository->getEnvData();
        // $envData['mail']['MAIL_USERNAME'] = str_replace('"', '', $envData['mail']['MAIL_USERNAME']);
        // $envData['mail']['MAIL_PASSWORD'] = str_replace('"', '', $envData['mail']['MAIL_PASSWORD']);
        // $envData['mail']['MAIL_FROM_ADDRESS'] = str_replace('"', '', $envData['mail']['MAIL_FROM_ADDRESS']);
        $setting = Setting::pluck('value', 'key')->toArray();
        $setting['phone'] = preparePhoneNumber($setting['phone'], $setting['region_code']);
        $sectionName = ($request->section === null) ? 'general' : $request->section;
        $envSetting = EnvSetting::pluck('value', 'key')->toArray();
        $languages = Language::toBase()->pluck('language', 'iso_code');

        return view(
            "settings.{$sectionName}",
            compact('setting', 'sectionName', 'envSetting', 'languages')
        )->with($envData);
    }

    /**
     * @throws DotEnvException
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->settingRepository->updateSetting($request->all());
        $language = $request->default_language;
        if (! empty($language)) {
            Session::put('languageName', $language);
        }

        //        Flash::error('Settings can not be updated on demo.');

        Flash::success(__('messages.flash.setting_update'));
        //         in order to clear the cache for .env values
        if ($request->get('sectionName') == 'env_setting') {
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
        }

        return Redirect::back();
    }
}
