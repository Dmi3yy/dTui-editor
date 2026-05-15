<?php namespace EvolutionCMS\dTuiEditor;

use EvolutionCMS\ServiceProvider;

class DTuiEditorServiceProvider extends ServiceProvider
{
    protected string $root;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->root = dirname(__DIR__);
    }

    public function boot(): void
    {
        $this->mergeConfigFrom($this->root . '/config/dTuiEditorCheck.php', 'cms.settings');
        $this->loadViewsFrom($this->root . '/views', 'dTuiEditor');
        $this->registerRoutes();
        $this->ensureRuntimeAssetsArePublished();

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
            $this->root . '/config/dTuiEditorSettings.php' => config_path('cms/settings/dTuiEditor.php', true),
            $this->root . '/config/which_editor.php' => config_path('cms/settings/which_editor.php', true),
        ], 'dtui-editor-config');

        $assetFiles = $this->collectPublishFiles(
            $this->root . '/public',
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

    protected function ensureRuntimeAssetsArePublished(): void
    {
        $sourceDir = $this->root . '/public';
        $targetDir = public_path('assets/plugins/dTui.editor');

        if (!is_dir($sourceDir)) {
            return;
        }

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

            $this->ensureRuntimeAsset(
                $path,
                $targetDir . DIRECTORY_SEPARATOR . $relative
            );
        }
    }

    protected function ensureRuntimeAsset(string $source, string $target): void
    {
        if (!is_file($source)) {
            return;
        }

        $targetDir = dirname($target);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        if (!is_dir($targetDir)) {
            return;
        }

        if (is_link($target)) {
            if (readlink($target) === $source) {
                return;
            }

            @unlink($target);
        }

        if (is_file($target) && filemtime($target) >= filemtime($source) && filesize($target) === filesize($source)) {
            return;
        }

        if (!file_exists($target) && @symlink($source, $target)) {
            return;
        }

        @copy($source, $target);
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
