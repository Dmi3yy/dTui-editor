<?php

// Run with: php tests/image-upload-csrf.php /path/to/evolution/core
// EVO_AUTOLOAD may point to another Composer autoloader when the core has no vendor directory.
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Facade;

function app($abstract = null, array $parameters = [])
{
    $container = Container::getInstance();
    return $abstract === null ? $container : $container->make($abstract, $parameters);
}

// Isolate CMS settings and permissions without booting a database-backed site.
function evo()
{
    return new class {
        public array $config = ['manager_language' => 'english'];

        public function getConfig($key)
        {
            return ['upload_maxsize' => 1024, 'upload_images' => 'png'][$key] ?? null;
        }

        public function hasPermission(...$args): bool
        {
            return false;
        }
    };
}

$core = rtrim($argv[1] ?? '', '/');
if (!is_file($core . '/src/Middleware/VerifyCsrfToken.php')) {
    throw new RuntimeException('Pass the path to an Evolution CMS 3.5.8+ core directory.');
}
require getenv('EVO_AUTOLOAD') ?: $core . '/vendor/autoload.php';
require $core . '/src/Middleware/VerifyCsrfToken.php';
if (is_file($core . '/src/Support/CsrfMessage.php')) {
    require $core . '/src/Support/CsrfMessage.php';
}

final class UploadTestSession
{
    public static array $session = [];

    public function handle($request, Closure $next)
    {
        $_SESSION = self::$session;
        return $next($request);
    }
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$app = new Container();
Container::setInstance($app);
Facade::setFacadeApplication($app);
$app->instance('config', new Repository(['app' => ['middleware' => ['global' => [UploadTestSession::class]]]]));
$factory = new class {
    public function json($data, $status = 200, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
};
$app->instance(Illuminate\Contracts\Routing\ResponseFactory::class, $factory);
$app->instance(Illuminate\Routing\Contracts\CallableDispatcher::class, new CallableDispatcher($app));
Illuminate\Support\Facades\Response::swap($factory);
$router = new Router(new Dispatcher($app), $app);
$app->instance('router', $router);
require dirname(__DIR__) . '/src/Http/routes.php';

$session = ['mgrValidated' => 1, 'mgrRole' => 1, '_token' => str_repeat('a', 40)];
$cases = [
    'missing token' => [$session, [], [], 403],
    'wrong token' => [$session, ['_token' => 'stale'], [], 403],
    'array token' => [$session, ['_token' => ['bad']], [], 403],
    'missing session token' => [array_diff_key($session, ['_token' => true]), ['_token' => $session['_token']], [], 403],
    'matching body token reaches file validation' => [$session, ['_token' => $session['_token']], [], 422],
    'matching header token reaches file validation' => [$session, [], ['HTTP_X_CSRF_TOKEN' => $session['_token']], 422],
    'empty body token cannot fall back to header' => [$session, ['_token' => ''], ['HTTP_X_CSRF_TOKEN' => $session['_token']], 403],
    'anonymous upload remains forbidden' => [[], [], [], 403],
    'valid token does not bypass upload permission' => [array_replace($session, ['mgrRole' => 2]), ['_token' => $session['_token']], [], 403],
];
foreach ($cases as $label => [$state, $body, $headers, $status]) {
    UploadTestSession::$session = $state;
    $request = Request::create('/dtui-image-upload', 'POST', $body, [], [], $headers + ['HTTP_ACCEPT' => 'application/json']);
    $app->instance('request', $request);
    $response = $router->dispatch($request);
    check($response->getStatusCode() === $status, $label . ': unexpected status ' . $response->getStatusCode());
    $payload = json_decode($response->getContent(), true);
    if (in_array($label, ['anonymous upload remains forbidden', 'valid token does not bypass upload permission'], true)) {
        check(($payload['message'] ?? null) === 'Forbidden', $label . ': permission check did not run');
    } elseif ($status === 403) {
        check(($payload['code'] ?? null) === 'csrf_token_mismatch', $label . ': CSRF middleware did not reject the request');
    } else {
        check(($payload['message'] ?? null) === 'No valid image was uploaded.', $label . ': upload handler did not run');
    }
    echo 'PASS: ', $label, PHP_EOL;
}

// Exercise actual file writes, and ensure forged requests cannot create the upload directory.
$temporaryRoot = sys_get_temp_dir() . '/dtui-csrf-' . bin2hex(random_bytes(8));
mkdir($temporaryRoot);
define('EVO_BASE_PATH', $temporaryRoot);
$png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a3ioAAAAASUVORK5CYII=');
try {
    foreach ([null, 'stale', $session['_token']] as $token) {
        $temporaryImage = $temporaryRoot . '/clipboard.png';
        file_put_contents($temporaryImage, $png);
        UploadTestSession::$session = $session;
        $request = Request::create('/dtui-image-upload', 'POST', $token === null ? [] : ['_token' => $token], [], [
            'image' => new Illuminate\Http\UploadedFile($temporaryImage, 'clipboard.png', 'image/png', null, true),
        ], ['HTTP_ACCEPT' => 'application/json']);
        $app->instance('request', $request);
        $response = $router->dispatch($request);
        if ($token !== $session['_token']) {
            check($response->getStatusCode() === 403, 'Forged upload was not rejected');
            check(!is_dir($temporaryRoot . '/assets'), 'Forged upload created an upload directory');
        } else {
            check($response->getStatusCode() === 200, 'Valid upload failed: ' . $response->getContent());
            $payload = json_decode($response->getContent(), true);
            check($payload['success'] === true, 'Valid upload was not successful');
            check(file_get_contents($temporaryRoot . $payload['url']) === $png, 'Uploaded image bytes changed');
        }
    }
    echo 'PASS: forged uploads write nothing; a valid token uploads the image', PHP_EOL;
} finally {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($temporaryRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($temporaryRoot);
}
