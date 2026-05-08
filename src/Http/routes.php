<?php

use EvolutionCMS\Models\SiteContent;
use Illuminate\Support\Facades\Route;

if (!function_exists('dTuiEditor_managerCanUploadImages')) {
    function dTuiEditor_managerCanUploadImages(): bool
    {
        if (!isset($_SESSION['mgrValidated'])) {
            return false;
        }

        if ((int)($_SESSION['mgrRole'] ?? 0) === 1) {
            return true;
        }

        return function_exists('evo') && (bool)evo()->hasPermission('file_manager', 'mgr');
    }
}

if (!function_exists('dTuiEditor_uploadError')) {
    function dTuiEditor_uploadError(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}

if (!function_exists('dTuiEditor_normalizeUploadTarget')) {
    function dTuiEditor_normalizeUploadTarget(string $configuredPath, string $basePath): ?array
    {
        $relative = str_replace('\\', '/', trim($configuredPath));
        $relative = trim($relative, "/ \t\n\r\0\x0B");

        if ($relative === '') {
            $relative = 'assets/images';
        }

        $parts = [];
        foreach (explode('/', $relative) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..' || !preg_match('/^[a-zA-Z0-9._-]+$/', $part)) {
                return null;
            }
            $parts[] = $part;
        }

        if ($parts === []) {
            $parts = ['assets', 'images'];
        }

        $relative = implode('/', $parts);
        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        return [
            'path' => $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative),
            'url' => '/' . $relative,
        ];
    }
}

if (!function_exists('dTuiEditor_plantUmlEncode6bit')) {
    function dTuiEditor_plantUmlEncode6bit(int $value): string
    {
        $value &= 0x3f;
        if ($value < 10) {
            return chr(48 + $value);
        }
        $value -= 10;
        if ($value < 26) {
            return chr(65 + $value);
        }
        $value -= 26;
        if ($value < 26) {
            return chr(97 + $value);
        }
        $value -= 26;
        return $value === 0 ? '-' : '_';
    }
}

if (!function_exists('dTuiEditor_plantUmlDecode6bit')) {
    function dTuiEditor_plantUmlDecode6bit(string $char): int
    {
        $code = ord($char);
        if ($code >= 48 && $code <= 57) {
            return $code - 48;
        }
        if ($code >= 65 && $code <= 90) {
            return $code - 55;
        }
        if ($code >= 97 && $code <= 122) {
            return $code - 61;
        }
        if ($char === '-') {
            return 62;
        }
        if ($char === '_') {
            return 63;
        }
        return 0;
    }
}

if (!function_exists('dTuiEditor_plantUmlAppend3Bytes')) {
    function dTuiEditor_plantUmlAppend3Bytes(int $b1, int $b2, int $b3): string
    {
        $c1 = $b1 >> 2;
        $c2 = (($b1 & 0x3) << 4) | ($b2 >> 4);
        $c3 = (($b2 & 0xf) << 2) | ($b3 >> 6);
        $c4 = $b3 & 0x3f;

        return dTuiEditor_plantUmlEncode6bit($c1)
            . dTuiEditor_plantUmlEncode6bit($c2)
            . dTuiEditor_plantUmlEncode6bit($c3)
            . dTuiEditor_plantUmlEncode6bit($c4);
    }
}

if (!function_exists('dTuiEditor_plantUmlEncode')) {
    function dTuiEditor_plantUmlEncode(string $text): string
    {
        $data = gzdeflate($text, 9);
        $output = '';
        $length = strlen($data);

        for ($i = 0; $i < $length; $i += 3) {
            $b1 = ord($data[$i]);
            $b2 = $i + 1 < $length ? ord($data[$i + 1]) : 0;
            $b3 = $i + 2 < $length ? ord($data[$i + 2]) : 0;
            $output .= dTuiEditor_plantUmlAppend3Bytes($b1, $b2, $b3);
        }

        return $output;
    }
}

if (!function_exists('dTuiEditor_plantUmlDecode')) {
    function dTuiEditor_plantUmlDecode(string $encoded): ?string
    {
        $data = '';
        $length = strlen($encoded);

        for ($i = 0; $i < $length; $i += 4) {
            $c1 = dTuiEditor_plantUmlDecode6bit($encoded[$i] ?? '0');
            $c2 = dTuiEditor_plantUmlDecode6bit($encoded[$i + 1] ?? '0');
            $c3 = dTuiEditor_plantUmlDecode6bit($encoded[$i + 2] ?? '0');
            $c4 = dTuiEditor_plantUmlDecode6bit($encoded[$i + 3] ?? '0');
            $data .= chr(($c1 << 2) | (($c2 & 0x30) >> 4));
            $data .= chr((($c2 & 0xf) << 4) | (($c3 & 0x3c) >> 2));
            $data .= chr((($c3 & 0x3) << 6) | $c4);
        }

        $decoded = @gzinflate($data);
        return is_string($decoded) ? $decoded : null;
    }
}

if (!function_exists('dTuiEditor_plantUmlDarkSkin')) {
    function dTuiEditor_plantUmlDarkSkin(): string
    {
        return implode("\n", [
            'skinparam backgroundColor #111827',
            'skinparam shadowing false',
            'skinparam defaultFontColor #E5E7EB',
            'skinparam ArrowColor #60A5FA',
            'skinparam ArrowFontColor #E5E7EB',
            'skinparam ClassBackgroundColor #1F2937',
            'skinparam ClassBorderColor #93C5FD',
            'skinparam ClassFontColor #E5E7EB',
            'skinparam ObjectBackgroundColor #1F2937',
            'skinparam ObjectBorderColor #93C5FD',
            'skinparam ObjectFontColor #E5E7EB',
            'skinparam ParticipantBackgroundColor #1F2937',
            'skinparam ParticipantBorderColor #93C5FD',
            'skinparam ParticipantFontColor #E5E7EB',
            'skinparam ActorBackgroundColor #1F2937',
            'skinparam ActorBorderColor #93C5FD',
            'skinparam ActorFontColor #E5E7EB',
            'skinparam NoteBackgroundColor #312E1F',
            'skinparam NoteBorderColor #F59E0B',
            'skinparam NoteFontColor #FDE68A',
            'skinparam SequenceLifeLineBorderColor #64748B',
        ]);
    }
}

if (!function_exists('dTuiEditor_plantUmlApplyDarkSkin')) {
    function dTuiEditor_plantUmlApplyDarkSkin(string $source): string
    {
        $skin = dTuiEditor_plantUmlDarkSkin();
        if (preg_match('/^\s*@startuml\b[^\n]*(\r?\n)?/i', $source, $match)) {
            $prefix = $match[0];
            return $prefix . $skin . "\n" . substr($source, strlen($prefix));
        }

        return $skin . "\n" . $source;
    }
}

Route::middleware(config('app.middleware.global', []))->get('dtui-plantuml', function () {
    $settings = config('cms.settings.dTuiEditor', []);
    $rendererURL = (string)($settings['plugins']['uml']['options']['rendererURL'] ?? 'https://www.plantuml.com/plantuml/png/');
    $rendererURL = rtrim($rendererURL, '/') . '/';
    $theme = (string)request('theme', 'light');
    $encoded = (string)request('uml', '');
    if ($encoded === '' || strlen($encoded) > 10000 || !preg_match('/^[A-Za-z0-9_-]+$/', $encoded)) {
        return response('', 404);
    }

    $source = dTuiEditor_plantUmlDecode($encoded);

    if ($source === null) {
        return redirect()->away($rendererURL . $encoded);
    }

    if (in_array($theme, ['dark', 'darkness'], true)) {
        $encoded = dTuiEditor_plantUmlEncode(dTuiEditor_plantUmlApplyDarkSkin($source));
    }

    return redirect()->away($rendererURL . $encoded);
});

Route::middleware(config('app.middleware.global', []))->get('dtui-evo-link-search', function () {
    if (!isset($_SESSION['mgrValidated'])) {
        return response()->json([], 403);
    }

    $evo = evo();
    if (empty($evo->config)) {
        $evo->getSettings();
    }

    $query = trim((string)request('q', ''));
    if ($query === '') {
        return response()->json([]);
    }

    $limit = (int)request('limit', 10);
    if ($limit <= 0) {
        $limit = 10;
    }
    if ($limit > 50) {
        $limit = 50;
    }

    $includeUnpublished = request('includeUnpublished') === '1';
    $includeHidden = request('includeHidden') === '1';

    $like = '%' . $query . '%';
    $builder = SiteContent::query()
        ->select(['id', 'pagetitle', 'alias'])
        ->where('deleted', 0)
        ->where(function ($sub) use ($like) {
            $sub->where('pagetitle', 'LIKE', $like)
                ->orWhere('alias', 'LIKE', $like);
        });

    if (!$includeUnpublished) {
        $builder->where('published', 1);
    }
    if (!$includeHidden) {
        $builder->where('searchable', 1);
    }

    $rows = $builder->orderBy('pagetitle', 'ASC')->limit($limit)->get()->toArray();

    $lowerQuery = strtolower($query);
    usort($rows, function ($a, $b) use ($lowerQuery) {
        $aTitle = strtolower((string)($a['pagetitle'] ?? ''));
        $bTitle = strtolower((string)($b['pagetitle'] ?? ''));
        $aStarts = strpos($aTitle, $lowerQuery) === 0 ? 0 : 1;
        $bStarts = strpos($bTitle, $lowerQuery) === 0 ? 0 : 1;
        if ($aStarts !== $bStarts) {
            return $aStarts - $bStarts;
        }
        return strcmp($aTitle, $bTitle);
    });

    $output = [];
    foreach ($rows as $row) {
        $id = (int)($row['id'] ?? 0);
        if ($id <= 0) {
            continue;
        }

        $pagetitle = (string)($row['pagetitle'] ?? '');
        $alias = (string)($row['alias'] ?? '');

        $uri = '';
        $url = '';
        if (method_exists($evo, 'makeUrl')) {
            $uri = (string)$evo->makeUrl($id);
            $url = (string)$evo->makeUrl($id, '', '', 'full');
        }

        if ($uri !== '' && strpos($uri, '://') !== false) {
            $parsed = parse_url($uri);
            if ($parsed && isset($parsed['path'])) {
                $uri = $parsed['path'];
            }
        }
        $uri = ltrim($uri, '/');

        if ($url === '' && $uri !== '') {
            $siteUrl = defined('EVO_SITE_URL') ? EVO_SITE_URL : '/';
            $url = rtrim($siteUrl, '/') . '/' . $uri;
        }

        $output[] = [
            'id' => $id,
            'pagetitle' => $pagetitle,
            'title' => $pagetitle,
            'alias' => $alias,
            'uri' => $uri,
            'url' => $url,
        ];
    }

    return response()->json($output);
});

Route::middleware(config('app.middleware.global', []))->post('dtui-image-upload', function () {
    if (!dTuiEditor_managerCanUploadImages()) {
        return dTuiEditor_uploadError('Forbidden', 403);
    }

    $file = request()->file('image') ?: request()->file('file');
    if (!$file || !$file->isValid()) {
        return dTuiEditor_uploadError('No valid image was uploaded.');
    }

    $evo = evo();
    if (empty($evo->config)) {
        $evo->getSettings();
    }

    $maxSize = (int)$evo->getConfig('upload_maxsize');
    if ($maxSize > 0 && $file->getSize() > $maxSize) {
        return dTuiEditor_uploadError('Image is too large.');
    }

    $extension = strtolower((string)$file->getClientOriginalExtension());
    $allowed = array_filter(array_map('trim', explode(',', (string)$evo->getConfig('upload_images'))));
    $allowed = array_map('strtolower', $allowed);
    if ($extension === '' || ($allowed !== [] && !in_array($extension, $allowed, true))) {
        return dTuiEditor_uploadError('Image type is not allowed.');
    }

    $mime = (string)$file->getMimeType();
    if ($mime !== '' && strpos($mime, 'image/') !== 0) {
        return dTuiEditor_uploadError('Uploaded file is not an image.');
    }

    $settings = config('cms.settings.dTuiEditor', []);
    $configuredUploadPath = (string)($settings['plugins']['image']['options']['uploadPath'] ?? 'assets/images');
    $basePath = defined('EVO_BASE_PATH') ? EVO_BASE_PATH : base_path('../');
    $uploadTarget = dTuiEditor_normalizeUploadTarget($configuredUploadPath, $basePath);
    if ($uploadTarget === null) {
        return dTuiEditor_uploadError('Invalid upload path configuration.', 500);
    }

    $uploadPath = $uploadTarget['path'];
    if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
        return dTuiEditor_uploadError('Cannot create upload directory.', 500);
    }
    if (!is_writable($uploadPath)) {
        return dTuiEditor_uploadError('Upload directory is not writable.', 500);
    }

    $originalName = pathinfo((string)$file->getClientOriginalName(), PATHINFO_FILENAME);
    $baseName = strtolower((string)preg_replace('/[^a-zA-Z0-9_-]+/', '-', $originalName));
    $baseName = trim($baseName, '-_') ?: 'image';
    $random = bin2hex(random_bytes(4));
    $filename = 'dtui-' . date('Ymd-His') . '-' . $random . '-' . $baseName . '.' . $extension;

    $file->move($uploadPath, $filename);

    return response()->json([
        'success' => true,
        'url' => rtrim($uploadTarget['url'], '/') . '/' . $filename,
        'alt' => $originalName ?: 'image',
        'name' => $filename,
    ]);
});
