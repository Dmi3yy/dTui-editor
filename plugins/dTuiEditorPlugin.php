<?php

if (!function_exists('dTuiEditor_log')) {
    function dTuiEditor_log(string $message, int $type = 2): void
    {
        if (function_exists('evo')) {
            evo()->logEvent(0, $type, $message, 'dTuiEditor');
        }
    }
}

if (!function_exists('dTuiEditor_basePath')) {
    function dTuiEditor_basePath(): string
    {
        if (defined('EVO_BASE_PATH')) {
            return rtrim(EVO_BASE_PATH, '/\\') . DIRECTORY_SEPARATOR;
        }

        if (defined('MODX_BASE_PATH')) {
            return rtrim(MODX_BASE_PATH, '/\\') . DIRECTORY_SEPARATOR;
        }

        return rtrim(dirname(__DIR__), '/\\') . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('dTuiEditor_siteUrl')) {
    function dTuiEditor_siteUrl(): string
    {
        if (defined('EVO_SITE_URL')) {
            return rtrim(EVO_SITE_URL, '/') . '/';
        }

        if (defined('MODX_SITE_URL')) {
            return rtrim(MODX_SITE_URL, '/') . '/';
        }

        return '/';
    }
}

if (!function_exists('dTuiEditor_baseUrl')) {
    function dTuiEditor_baseUrl(): string
    {
        $baseUrl = '/';

        if (defined('EVO_BASE_URL')) {
            $baseUrl = EVO_BASE_URL;
        } elseif (defined('MODX_BASE_URL')) {
            $baseUrl = MODX_BASE_URL;
        }

        $baseUrl = '/' . ltrim((string) $baseUrl, '/');

        return rtrim($baseUrl, '/');
    }
}

if (!function_exists('dTuiEditor_getManagerThemeMode')) {
    function dTuiEditor_getManagerThemeMode(): ?string
    {
        $themeModes = ['', 'lightness', 'light', 'dark', 'darkness'];

        if (isset($_COOKIE['EVO_themeMode'])) {
            $index = (int)$_COOKIE['EVO_themeMode'];
            if (!empty($themeModes[$index])) {
                return $themeModes[$index];
            }
        }

        if (isset($_COOKIE['MODX_themeMode'])) {
            $index = (int)$_COOKIE['MODX_themeMode'];
            if (!empty($themeModes[$index])) {
                return $themeModes[$index];
            }
        }

        $configMode = (int)evo()->getConfig('manager_theme_mode');
        if (!empty($themeModes[$configMode])) {
            return $themeModes[$configMode];
        }

        return null;
    }
}

if (!function_exists('dTuiEditor_normalizeTheme')) {
    function dTuiEditor_normalizeTheme(?string $theme, array $themes): string
    {
        $theme = $theme ? strtolower(trim($theme)) : 'auto';
        if ($theme === 'auto') {
            $managerTheme = dTuiEditor_getManagerThemeMode();
            return $managerTheme ?: 'light';
        }

        return isset($themes[$theme]) && $theme !== 'auto' ? $theme : 'light';
    }
}

if (!function_exists('dTuiEditor_normalizeEditorMode')) {
    function dTuiEditor_normalizeEditorMode(?string $mode, array $modes): string
    {
        $mode = $mode ? strtolower(trim($mode)) : '';
        if ($mode === 'markdown-only' || $mode === 'md') {
            $mode = 'markdown';
        } elseif ($mode === 'wysiwyg-only' || $mode === 'ww') {
            $mode = 'wysiwyg';
        } elseif ($mode === 'vertical') {
            $mode = 'split';
        }

        return isset($modes[$mode]) ? $mode : 'wysiwyg';
    }
}

if (!function_exists('dTuiEditor_applyEditorMode')) {
    function dTuiEditor_applyEditorMode(array $options, string $mode): array
    {
        $options['editorMode'] = $mode;
        $options['hideModeSwitch'] = true;

        if ($mode === 'markdown') {
            $options['initialEditType'] = 'markdown';
            $options['previewStyle'] = 'tab';
        } elseif ($mode === 'split') {
            $options['initialEditType'] = 'markdown';
            $options['previewStyle'] = 'vertical';
        } else {
            $options['initialEditType'] = 'wysiwyg';
            $options['previewStyle'] = 'tab';
        }

        return $options;
    }
}

if (!function_exists('dTuiEditor_isDarkTheme')) {
    function dTuiEditor_isDarkTheme(string $theme): bool
    {
        return in_array($theme, ['dark', 'darkness'], true);
    }
}

if (!function_exists('dTuiEditor_boolSetting')) {
    function dTuiEditor_boolSetting(string $key, bool $default): bool
    {
        $value = evo()->getConfig($key);
        if ($value === null || $value === '') {
            return $default;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ((bool)$value);
    }
}

if (!function_exists('dTuiEditor_isValidSelector')) {
    function dTuiEditor_isValidSelector(string $selector): bool
    {
        return trim($selector) !== '';
    }
}

if (!function_exists('dTuiEditor_normalizeSelector')) {
    function dTuiEditor_normalizeSelector(string $selector): string
    {
        $selector = trim($selector);
        if ($selector === '') {
            return $selector;
        }

        $first = $selector[0] ?? '';
        if ($first === '#' || $first === '.' || $first === '[') {
            return $selector;
        }

        if (strpos($selector, ' ') !== false || strpos($selector, ':') !== false) {
            return $selector;
        }

        return '#' . $selector;
    }
}

if (!function_exists('dTuiEditor_pluginSettingKey')) {
    function dTuiEditor_pluginSettingKey(string $plugin): string
    {
        $snake = strtolower((string)preg_replace('/(?<!^)[A-Z]/', '_$0', $plugin));
        return 'dtui_plugin_' . $snake;
    }
}

if (!function_exists('dTuiEditor_enabledPlugins')) {
    function dTuiEditor_enabledPlugins(array $profilePlugins, array $settings): array
    {
        $plugins = $settings['plugins'] ?? [];
        $enabled = [];

        foreach ($profilePlugins as $plugin) {
            if (!is_string($plugin) || $plugin === '' || !isset($plugins[$plugin])) {
                continue;
            }

            $default = (bool)($plugins[$plugin]['enabled'] ?? true);
            if (!dTuiEditor_boolSetting(dTuiEditor_pluginSettingKey($plugin), $default)) {
                continue;
            }

            $enabled[] = $plugin;
        }

        return array_values(array_unique($enabled));
    }
}

if (!function_exists('dTuiEditor_assetVersion')) {
    function dTuiEditor_assetVersion(string $path, array $settings): string
    {
        $mtime = @filemtime($path);
        if (is_int($mtime)) {
            return '?v=' . $mtime;
        }

        $version = $settings['version'] ?? '';
        return is_string($version) && $version !== '' ? '?v=' . rawurlencode($version) : '';
    }
}

if (!function_exists('dTuiEditor_languageCode')) {
    function dTuiEditor_languageCode(array $settings): string
    {
        $default = (string)($settings['default_language'] ?? 'en-US');
        $languages = $settings['languages'] ?? [];

        $candidates = [];
        $feLanguage = evo()->getConfig('fe_editor_lang') ?: '';
        if ($feLanguage !== '') {
            $candidates[] = $feLanguage;
        }
        $managerLanguage = evo()->getConfig('manager_language') ?: '';
        if ($managerLanguage !== '') {
            $candidates[] = $managerLanguage;
        }

        foreach ($candidates as $candidate) {
            $candidate = strtolower(str_replace('_', '-', trim((string)$candidate)));
            if ($candidate === '') {
                continue;
            }

            foreach ($languages as $key => $code) {
                $normalizedKey = strtolower(str_replace('_', '-', (string)$key));
                $normalizedCode = strtolower(str_replace('_', '-', (string)$code));
                if ($candidate === $normalizedKey || $candidate === $normalizedCode || strtok($candidate, '-') === $normalizedKey) {
                    return (string)$code;
                }
            }
        }

        return $default;
    }
}

if (!function_exists('dTuiEditor_i18nAssetName')) {
    function dTuiEditor_i18nAssetName(string $language): string
    {
        $language = strtolower(str_replace('_', '-', trim($language)));
        if ($language === '' || $language === 'en' || $language === 'en-us') {
            return '';
        }

        return $language . '.js';
    }
}

if (!function_exists('dTuiEditor_assetTags')) {
    function dTuiEditor_assetTags(array $usedPlugins, string $theme, string $language, array $settings): array
    {
        $basePath = dTuiEditor_basePath() . 'assets/plugins/dTui.editor';
        $baseUrl = dTuiEditor_siteUrl() . 'assets/plugins/dTui.editor';

        $required = $basePath . '/vendor/toastui-editor-all.min.js';
        if (!is_file($required)) {
            dTuiEditor_log('Missing TOAST UI Editor assets. Run vendor:publish for dTui.editor.');
            return [
                '<script>console.warn("dTui.editor assets are not published.");</script>',
                '<script>document.addEventListener("DOMContentLoaded",function(){var el=document.querySelector("#main")||document.body;if(el){var d=document.createElement("div");d.className="alert alert-danger";d.textContent="dTui.editor assets are not published. Run vendor:publish.";el.prepend(d);}});</script>',
            ];
        }

        if (!isset($GLOBALS['DTUIEDITOR_LOADED_ASSETS']) || !is_array($GLOBALS['DTUIEDITOR_LOADED_ASSETS'])) {
            $GLOBALS['DTUIEDITOR_LOADED_ASSETS'] = [];
        }

        $css = ['vendor/toastui-editor.min.css'];
        $css[] = 'vendor/toastui-editor-dark.min.css';
        if (in_array('chart', $usedPlugins, true)) {
            $css[] = 'vendor/toastui-chart.min.css';
        }
        if (in_array('codeSyntaxHighlight', $usedPlugins, true)) {
            $css[] = 'vendor/prism.min.css';
            $css[] = 'vendor/toastui-editor-plugin-code-syntax-highlight.min.css';
        }
        if (in_array('colorSyntax', $usedPlugins, true)) {
            $css[] = 'vendor/tui-color-picker.min.css';
            $css[] = 'vendor/toastui-editor-plugin-color-syntax.min.css';
        }
        if (in_array('tableMergedCell', $usedPlugins, true)) {
            $css[] = 'vendor/toastui-editor-plugin-table-merged-cell.min.css';
        }
        $css[] = 'css/dtui-editor.css';

        $js = [];
        if (in_array('chart', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-chart.min.js';
        }
        if (in_array('codeSyntaxHighlight', $usedPlugins, true)) {
            $js[] = 'vendor/prism.min.js';
            $js[] = 'vendor/prism-evo-languages.min.js';
        }
        if (in_array('colorSyntax', $usedPlugins, true)) {
            $js[] = 'vendor/tui-color-picker.min.js';
        }
        $js[] = 'vendor/toastui-editor-all.min.js';
        $i18nAsset = dTuiEditor_i18nAssetName($language);
        if ($i18nAsset !== '') {
            $js[] = 'vendor/i18n/' . $i18nAsset;
        }
        if (in_array('chart', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-editor-plugin-chart.min.js';
        }
        if (in_array('codeSyntaxHighlight', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-editor-plugin-code-syntax-highlight.js';
        }
        if (in_array('colorSyntax', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-editor-plugin-color-syntax.min.js';
        }
        if (in_array('tableMergedCell', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-editor-plugin-table-merged-cell.min.js';
        }
        if (in_array('uml', $usedPlugins, true)) {
            $js[] = 'vendor/toastui-editor-plugin-uml.min.js';
        }
        if (in_array('image', $usedPlugins, true)) {
            $js[] = 'js/dtui-image.js';
        }
        if (in_array('evolinks', $usedPlugins, true)) {
            $js[] = 'js/dtui-evolinks.js';
        }
        $js[] = 'js/dtui-init.js';

        $tags = [];
        foreach (array_values(array_unique($css)) as $file) {
            $assetKey = 'css:' . $file;
            if (isset($GLOBALS['DTUIEDITOR_LOADED_ASSETS'][$assetKey])) {
                continue;
            }
            $path = $basePath . '/' . $file;
            if (is_file($path)) {
                $tags[] = '<link rel="stylesheet" href="' . $baseUrl . '/' . $file . dTuiEditor_assetVersion($path, $settings) . '" />';
                $GLOBALS['DTUIEDITOR_LOADED_ASSETS'][$assetKey] = true;
            } else {
                dTuiEditor_log('Missing dTui.editor CSS asset: ' . $file);
            }
        }
        foreach (array_values(array_unique($js)) as $file) {
            $assetKey = 'js:' . $file;
            if (isset($GLOBALS['DTUIEDITOR_LOADED_ASSETS'][$assetKey])) {
                continue;
            }
            $path = $basePath . '/' . $file;
            if (is_file($path)) {
                $tags[] = '<script src="' . $baseUrl . '/' . $file . dTuiEditor_assetVersion($path, $settings) . '"></script>';
                $GLOBALS['DTUIEDITOR_LOADED_ASSETS'][$assetKey] = true;
            } else {
                dTuiEditor_log('Missing dTui.editor JS asset: ' . $file);
            }
        }

        return $tags;
    }
}

Event::listen('evolution.OnRichTextEditorRegister', function () {
    return 'dTuiEditor';
});

Event::listen('evolution.OnInterfaceSettingsRender', function () {
    $settings = config('cms.settings.dTuiEditor', []);
    $profiles = $settings['profiles'] ?? [];
    $themes = $settings['themes'] ?? [];
    $editorModes = $settings['editor_modes'] ?? [];
    $plugins = $settings['plugins'] ?? [];

    $profileOptions = [];
    foreach ($profiles as $key => $profile) {
        $profileOptions[$key] = is_array($profile) && isset($profile['label']) ? $profile['label'] : $key;
    }

    $themeOptions = [];
    foreach ($themes as $key => $theme) {
        $themeOptions[$key] = is_array($theme) && isset($theme['label']) ? $theme['label'] : $key;
    }

    $modeOptions = [];
    foreach ($editorModes as $key => $mode) {
        $modeOptions[$key] = is_array($mode) && isset($mode['label']) ? $mode['label'] : $key;
    }

    $pluginOptions = [];
    foreach ($plugins as $key => $plugin) {
        $default = (bool)($plugin['enabled'] ?? true);
        $pluginOptions[$key] = [
            'label' => is_array($plugin) && isset($plugin['label']) ? $plugin['label'] : $key,
            'setting' => dTuiEditor_pluginSettingKey($key),
            'enabled' => dTuiEditor_boolSetting(dTuiEditor_pluginSettingKey($key), $default),
        ];
    }

    return \View::make('dTuiEditor::settings', [
        'profiles' => $profileOptions,
        'themes' => $themeOptions,
        'editorModes' => $modeOptions,
        'plugins' => $pluginOptions,
        'currentProfile' => evo()->getConfig('dtui_profile') ?: ($settings['default_profile'] ?? 'full'),
        'currentTheme' => evo()->getConfig('dtui_editor_theme') ?: ($settings['default_theme'] ?? 'auto'),
        'currentEditorMode' => dTuiEditor_normalizeEditorMode(
            evo()->getConfig('dtui_editor_mode') ?: ($settings['default_editor_mode'] ?? 'wysiwyg'),
            $editorModes
        ),
    ])->toHtml();
});

Event::listen('evolution.OnRichTextEditorInit', function ($params) {
    if (!isset($params['editor']) || !in_array($params['editor'], ['dTuiEditor', 'dTui.editor'], true)) {
        return '';
    }

    $elements = $params['elements'] ?? [];
    if (!is_array($elements) || $elements === []) {
        return '';
    }

    $settings = config('cms.settings.dTuiEditor', []);
    $profiles = $settings['profiles'] ?? [];
    $themes = $settings['themes'] ?? [];
    $editorModes = $settings['editor_modes'] ?? [];
    $pluginSettings = $settings['plugins'] ?? [];
    $protected = $settings['protected_keys'] ?? [];

    $defaultProfile = $settings['default_profile'] ?? 'full';
    $systemProfile = evo()->getConfig('dtui_profile') ?: $defaultProfile;
    if (!isset($profiles[$systemProfile])) {
        dTuiEditor_log('Unknown dtui_profile: ' . $systemProfile);
        $systemProfile = $defaultProfile;
    }

    $themeSetting = evo()->getConfig('dtui_editor_theme') ?: ($settings['default_theme'] ?? 'auto');
    $theme = dTuiEditor_normalizeTheme($themeSetting, $themes);
    $systemEditorMode = dTuiEditor_normalizeEditorMode(
        evo()->getConfig('dtui_editor_mode') ?: ($settings['default_editor_mode'] ?? 'wysiwyg'),
        $editorModes
    );
    $language = dTuiEditor_languageCode($settings);
    $optionsByField = $params['options'] ?? [];
    $eventHeight = is_string($params['height'] ?? null) ? trim($params['height']) : '';
    $editors = [];
    $usedPlugins = [];

    foreach ($elements as $element) {
        $selector = is_string($element) ? $element : '';
        if (!dTuiEditor_isValidSelector($selector)) {
            dTuiEditor_log('Invalid editor selector: ' . $selector);
            continue;
        }

        $fieldOptions = $optionsByField[$element] ?? [];
        if (!is_array($fieldOptions)) {
            $fieldOptions = [];
        }

        $profile = $fieldOptions['profile'] ?? $fieldOptions['theme'] ?? $systemProfile;
        if (!isset($profiles[$profile])) {
            dTuiEditor_log('Unknown profile: ' . $profile . '. Using default.');
            $profile = $defaultProfile;
        }

        $profileOptions = $profiles[$profile]['options'] ?? [];
        $fieldOverrides = $fieldOptions;
        foreach ($protected as $key) {
            unset($fieldOverrides[$key]);
        }
        unset($fieldOverrides['profile'], $fieldOverrides['theme'], $fieldOverrides['editor_mode'], $fieldOverrides['mode']);
        $fieldHeight = isset($fieldOverrides['height']) ? trim((string) $fieldOverrides['height']) : '';
        if ($eventHeight !== '' && $fieldHeight === '') {
            $fieldOverrides['height'] = $eventHeight;
        }

        $options = array_replace_recursive($profileOptions, $fieldOverrides);
        $editorMode = dTuiEditor_normalizeEditorMode(
            $fieldOptions['editorMode'] ?? $fieldOptions['editor_mode'] ?? $fieldOptions['mode'] ?? $profileOptions['editorMode'] ?? $systemEditorMode,
            $editorModes
        );
        $options['usageStatistics'] = (bool)($settings['usage_statistics'] ?? false);
        $options['height'] = $options['height'] ?? ($settings['default_height'] ?? '500px');
        $options = dTuiEditor_applyEditorMode($options, $editorMode);
        $options['language'] = $options['language'] ?? $language;
        if (dTuiEditor_isDarkTheme($theme)) {
            $options['theme'] = 'dark';
        }

        $profilePlugins = $profiles[$profile]['plugins'] ?? [];
        $enabledPlugins = dTuiEditor_enabledPlugins($profilePlugins, $settings);
        $usedPlugins = array_merge($usedPlugins, $enabledPlugins);

        $editors[] = [
            'selector' => dTuiEditor_normalizeSelector($selector),
            'name' => $element,
            'profile' => $profile,
            'plugins' => $enabledPlugins,
            'options' => $options,
        ];
    }

    if ($editors === []) {
        return '';
    }

    $baseUrl = dTuiEditor_siteUrl() . 'assets/plugins/dTui.editor';
    $siteBaseUrl = dTuiEditor_baseUrl();

    $efilemanagerSettings = config('cms.settings.eFilemanager', config('efilemanager', []));
    if (!is_array($efilemanagerSettings)) {
        $efilemanagerSettings = [];
    }

    $lfmUrlPrefix = function_exists('config') ? (string)config('lfm.url_prefix', 'filemanager') : 'filemanager';
    if ($lfmUrlPrefix === '') {
        $lfmUrlPrefix = 'filemanager';
    }

    $whichBrowser = evo()->getConfig('which_browser') ?: 'mcpuk';
    if ($whichBrowser !== 'efilemanager') {
        $whichBrowser = 'mcpuk';
    }

    $config = [
        'queue' => $editors,
        'baseUrl' => $siteBaseUrl,
        'assetBaseUrl' => $baseUrl,
        'theme' => $theme,
        'themeMode' => $theme,
        'followManagerTheme' => $themeSetting === '' || $themeSetting === 'auto',
        'darkThemes' => ['dark', 'darkness'],
        'language' => $language,
        'plugins' => $pluginSettings,
        'evoLinkSearchUrl' => rtrim($siteBaseUrl . '/' . ltrim($settings['routes']['evo_link_search'] ?? 'dtui-evo-link-search', '/'), '/') . '/',
        'imageUploadUrl' => rtrim($siteBaseUrl . '/' . ltrim($settings['routes']['image_upload'] ?? 'dtui-image-upload', '/'), '/') . '/',
        'plantUmlRendererUrl' => rtrim($siteBaseUrl . '/' . ltrim($settings['routes']['plantuml_renderer'] ?? 'dtui-plantuml', '/'), '/') . '/',
        'whichBrowser' => $whichBrowser,
        'opener' => 'tinymce',
        'fileManager' => [
            'enabled' => $whichBrowser === 'efilemanager',
            'urlPrefix' => $lfmUrlPrefix,
            'allowMcpukFallback' => (bool)($efilemanagerSettings['allow_mcpuk_fallback'] ?? true),
            'urlStrategy' => (string)($efilemanagerSettings['url_strategy'] ?? 'relative'),
            'allowSignedUrls' => (bool)($efilemanagerSettings['allow_signed_urls'] ?? false),
        ],
    ];

    $configJson = json_encode($config, JSON_UNESCAPED_SLASHES);
    if ($configJson === false) {
        dTuiEditor_log('Failed to encode init config.');
        return '';
    }

    $usedPlugins = array_values(array_unique($usedPlugins));
    $output = dTuiEditor_assetTags($usedPlugins, $theme, $language, $settings);
    $output[] = '<script>(function(config){window.dTuiEditorConfig=config;window.dTuiEditorConfigQueue=window.dTuiEditorConfigQueue||[];window.dTuiEditorConfigQueue.push(config);if(window.dTuiEditor&&typeof window.dTuiEditor.flush==="function"){window.dTuiEditor.flush();}})(' . $configJson . ');</script>';

    return implode("\n", $output);
});
