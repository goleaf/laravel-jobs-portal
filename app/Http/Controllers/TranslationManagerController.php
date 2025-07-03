<?php

namespace App\Http\Controllers;

use App\Helpers\LanguageHelper;
use App\Repositories\TranslationManagerRepository;
use App\Services\TranslationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class TranslationManagerController extends AppBaseController
{
    /**
     * @var TranslationManagerRepository
     */
    private $translateManagerRepo;

    public function __construct(TranslationManagerRepository $translateManagerRepo)
    {
        $this->translateManagerRepo = $translateManagerRepo;
    }

    /**
     * Display a listing of the translations.
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function index(Request $request)
    {
        $selectedLang = $request->get('name', 'en');
        $selectedFile = $request->get('file', 'messages.php');

        $langExists = $this->translateManagerRepo->checkLanguageExistOrNot($selectedLang);
        if (! $langExists) {
            return redirect()->back()->withErrors($selectedLang.' '.__('locale.validation.locale_unsupported'));
        }

        $fileExists = $this->translateManagerRepo->checkFileExistOrNot($selectedLang, $selectedFile);
        if (! $fileExists) {
            return redirect()->back()->withErrors($selectedFile.' '.__('messages.common.file_not_found'));
        }

        $oldLang = app()->getLocale();
        $data = $this->translateManagerRepo->getSubDirectoryFiles($selectedLang, $selectedFile);
        app()->setLocale($oldLang);

        // Add translation statistics
        $data['statistics'] = TranslationService::getStatistics();
        $data['availableLocales'] = Config::get('app.available_locales', []);

        return view('translation-manager.index', compact('selectedLang', 'selectedFile'))->with($data);
    }

    /**
     * Store a new language.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z]+$/u|min:2|max:2',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->getMessageBag()->getMessages()['name'][0]);
        }

        $input = $request->all();
        $this->translateManagerRepo->store($input);

        // Clear translation cache
        TranslationService::clearCache();

        return $this->sendSuccess(__('messages.flash.language_added'));
    }

    /**
     * Update translations.
     */
    public function update(Request $request): RedirectResponse
    {
        $lName = $request->get('translate_language');
        $fileName = $request->get('file_name');

        $fileExists = $this->translateManagerRepo->checkFileExistOrNot($lName, $fileName);
        if (! $fileExists) {
            return redirect()->back()->withErrors(__('messages.common.file_not_found'));
        }

        if (! empty($lName)) {
            $result = $request->except(['_token', 'translate_language', 'file_name']);
            File::put(base_path('lang/'.$lName.'/'.$fileName), '<?php return '.var_export($result, true).'?>');

            // Clear translation cache
            TranslationService::clearCache();
        }

        Flash::success(__('messages.flash.translation_update'));

        return redirect()->route('translations.index');
    }

    /**
     * Get translation statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = TranslationService::getStatistics();
        $availableLocales = Config::get('app.available_locales', []);

        $enhancedStats = [];
        foreach ($stats as $locale => $stat) {
            $enhancedStats[$locale] = array_merge($stat, [
                'locale_info' => $availableLocales[$locale] ?? [],
                'is_rtl' => LanguageHelper::isRtl($locale),
                'flag' => $this->getFlag($locale),
            ]);
        }

        return $this->sendResponse($enhancedStats, __('locale.messages.translations_loaded'));
    }

    /**
     * Get missing translation keys for a locale.
     */
    public function missing(string $locale): JsonResponse
    {
        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (! in_array($locale, $availableLocales)) {
            return $this->sendError(__('locale.validation.locale_unsupported'));
        }

        $missingKeys = TranslationService::getMissingKeys($locale);

        return $this->sendResponse([
            'locale' => $locale,
            'missing_keys' => $missingKeys,
            'total_missing' => count($missingKeys),
        ], __('messages.flash.data_retrieved'));
    }

    /**
     * Sync translations from base locale to target locale.
     */
    public function sync(Request $request, string $locale): JsonResponse
    {
        $baseLocale = $request->input('base_locale', 'en');
        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (! in_array($locale, $availableLocales) || ! in_array($baseLocale, $availableLocales)) {
            return $this->sendError(__('locale.validation.locale_unsupported'));
        }

        try {
            $baseTranslations = TranslationService::getAllTranslations($baseLocale);
            $targetTranslations = TranslationService::getAllTranslations($locale);
            $missingKeys = TranslationService::getMissingKeys($locale, $baseLocale);

            $synced = 0;
            foreach ($missingKeys as $key) {
                if (isset($baseTranslations[$key])) {
                    // Create placeholder translation
                    $targetTranslations[$key] = "[{$locale}] ".$baseTranslations[$key];
                    $synced++;
                }
            }

            // Save to JSON file
            $filePath = lang_path("{$locale}.json");
            File::put($filePath, json_encode($targetTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Clear cache
            TranslationService::clearCache();

            return $this->sendResponse([
                'synced_keys' => $synced,
                'total_missing' => count($missingKeys),
            ], __('messages.flash.translation_sync_success', ['count' => $synced]));
        } catch (\Exception $e) {
            return $this->sendError(__('messages.flash.translation_sync_failed').': '.$e->getMessage());
        }
    }

    /**
     * Export translations for a locale.
     */
    public function export(string $locale): Response
    {
        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (! in_array($locale, $availableLocales)) {
            abort(400, __('locale.validation.locale_unsupported'));
        }

        $translations = TranslationService::getAllTranslations($locale);
        $jsonContent = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($jsonContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$locale}-translations.json\"");
    }

    /**
     * Import translations for a locale.
     */
    public function import(Request $request, string $locale): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
            'merge' => 'boolean',
        ]);

        $availableLocales = array_keys(Config::get('app.available_locales', []));

        if (! in_array($locale, $availableLocales)) {
            return $this->sendError(__('locale.validation.locale_unsupported'));
        }

        try {
            $file = $request->file('file');
            $content = File::get($file->path());
            $importedTranslations = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->sendError(__('messages.flash.invalid_json_file'));
            }

            $merge = $request->boolean('merge', true);
            $targetTranslations = [];

            if ($merge) {
                $targetTranslations = TranslationService::getAllTranslations($locale);
            }

            $targetTranslations = array_merge($targetTranslations, $importedTranslations);

            // Save to JSON file
            $filePath = lang_path("{$locale}.json");
            File::put($filePath, json_encode($targetTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Clear cache
            TranslationService::clearCache();

            return $this->sendResponse([
                'imported_keys' => count($importedTranslations),
                'total_keys' => count($targetTranslations),
            ], __('messages.flash.translation_import_success', ['count' => count($importedTranslations)]));
        } catch (\Exception $e) {
            return $this->sendError(__('messages.flash.translation_import_failed').': '.$e->getMessage());
        }
    }

    /**
     * Get flag emoji for locale.
     */
    private function getFlag(string $locale): string
    {
        $flags = [
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'tr' => '🇹🇷',
            'zh' => '🇨🇳',
        ];

        return $flags[$locale] ?? '🌐';
    }
}
