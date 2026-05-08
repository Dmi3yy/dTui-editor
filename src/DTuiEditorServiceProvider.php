<?php namespace EvolutionCMS\dTuiEditor;

use EvolutionCMS\ServiceProvider;

class DTuiEditorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/dTuiEditorCheck.php', 'cms.settings');
        $this->loadViewsFrom(dirname(__DIR__) . '/views', 'dTuiEditor');
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    public function register(): void
    {
        $this->loadPluginsFrom(dirname(__DIR__) . '/plugins/');
    }

    protected function publishResources(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/config/dTuiEditorSettings.php' => config_path('cms/settings/dTuiEditor.php', true),
            dirname(__DIR__) . '/config/which_editor.php' => config_path('cms/settings/which_editor.php', true),
        ], 'dtui-editor-config');

        $assetFiles = $this->collectPublishFiles(
            dirname(__DIR__) . '/public',
            public_path('assets/plugins/dTui.editor')
        );

        if ($assetFiles !== []) {
            $this->publishes($assetFiles, 'dtui-editor-assets');
        }
    }

    protected function collectPublishFiles(string $sourceDir, string $targetDir): array
    {
        if (!is_dir($sourceDir)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS)
        );

        $sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);
        $targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $path = $file->getPathname();
            $relative = substr($path, strlen($sourceDir) + 1);
            $files[$path] = $targetDir . DIRECTORY_SEPARATOR . $relative;
        }

        return $files;
    }

    protected function registerRoutes(): void
    {
        if (defined('IN_MANAGER_MODE') && IN_MANAGER_MODE === true) {
            return;
        }

        $routesPath = __DIR__ . '/Http/routes.php';
        if (is_file($routesPath)) {
            include $routesPath;
        }
    }
}
