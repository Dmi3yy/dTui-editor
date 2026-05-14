<?php

$root = dirname(__DIR__);
$errors = [];

$markdownFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getFilename() === '.DS_Store') {
        $errors[] = ltrim(str_replace($root, 'docs', $file->getPathname()), DIRECTORY_SEPARATOR) . ' must not be committed or indexed';
        continue;
    }

    if ($file->isFile() && in_array(strtolower($file->getExtension()), ['md', 'mdx'], true)) {
        $markdownFiles[] = $file->getPathname();
    }
}

foreach ($markdownFiles as $path) {
    $relative = ltrim(str_replace($root, 'docs', $path), DIRECTORY_SEPARATOR);
    $text = file_get_contents($path) ?: '';
    $lines = preg_split('/\R/', $text) ?: [];

    $h1 = 0;
    $previousLevel = 0;
    $inFence = false;
    $fenceChar = '';
    $fenceLength = 0;

    foreach ($lines as $index => $line) {
        $lineNumber = $index + 1;

        if (!$inFence && preg_match('/^(`{3,}|~{3,})(.*)$/', rtrim($line), $match)) {
            if (trim($match[2]) === '') {
                $errors[] = "{$relative}:{$lineNumber} code fence is missing a language";
            }
            $inFence = true;
            $fenceChar = $match[1][0];
            $fenceLength = strlen($match[1]);
            continue;
        }

        if ($inFence && preg_match('/^' . preg_quote(str_repeat($fenceChar, $fenceLength), '/') . '\s*$/', rtrim($line))) {
            $inFence = false;
            continue;
        }

        if ($inFence) {
            continue;
        }

        if (preg_match('/^(#{1,6})\s+\S/', $line, $match)) {
            $level = strlen($match[1]);
            if ($level === 1) {
                $h1++;
            }
            if ($previousLevel > 0 && $level > $previousLevel + 1) {
                $errors[] = "{$relative}:{$lineNumber} heading level skips from H{$previousLevel} to H{$level}";
            }
            $previousLevel = $level;
        }
    }

    if ($h1 !== 1) {
        $errors[] = "{$relative} must have exactly one H1, found {$h1}";
    }

    $textWithoutFences = preg_replace('/(^|\n)(`{3,}|~{3,})[^\n]*\n[\s\S]*?\n\2[ \t]*(?=\n|$)/', "\n", $text) ?: $text;

    if (preg_match('/(^|[\s(])\/Users\/[^\s)]*/', $textWithoutFences) === 1) {
        $errors[] = "{$relative} contains a local absolute filesystem path";
    }

    $linkText = $textWithoutFences;
    preg_match_all('/(?<!!)\[[^\]]+]\(([^)\s]+)(?:\s+[^)]*)?\)|!\[[^\]]*]\(([^)\s]+)(?:\s+[^)]*)?\)/', $linkText, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $href = $match[1] ?: $match[2];
        if ($href === '' || str_starts_with($href, '#') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $href) || str_starts_with($href, '//')) {
            continue;
        }

        $target = urldecode(explode('#', $href, 2)[0]);
        $base = dirname($path);
        $candidates = [$base . DIRECTORY_SEPARATOR . $target];
        if (!preg_match('/\.mdx?$/i', $target)) {
            $candidates[] = $base . DIRECTORY_SEPARATOR . $target . '.md';
            $candidates[] = $base . DIRECTORY_SEPARATOR . $target . DIRECTORY_SEPARATOR . 'README.md';
            $candidates[] = $base . DIRECTORY_SEPARATOR . $target . DIRECTORY_SEPARATOR . 'index.md';
        }

        $exists = false;
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $errors[] = "{$relative} has broken internal link: {$href}";
        }
    }
}

if (is_dir($root . DIRECTORY_SEPARATOR . 'ua')) {
    $errors[] = 'docs/ua must not exist; Ukrainian documentation locale is docs/uk only';
}

foreach (['en', 'uk', 'pl', 'de', 'fr'] as $locale) {
    $readme = $root . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'README.md';
    if (!is_file($readme)) {
        $errors[] = "docs/{$locale}/README.md is missing";
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

echo "dDocs documentation checks passed." . PHP_EOL;
