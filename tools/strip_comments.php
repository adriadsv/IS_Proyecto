<?php

declare(strict_types=1);

$apply = in_array('--apply', $argv, true);
$dryRun = in_array('--dry-run', $argv, true) || ! $apply;
$all = in_array('--all', $argv, true);

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "No se pudo resolver la ruta del proyecto." . PHP_EOL);
    exit(1);
}

$excluded = [
    'vendor',
    'node_modules',
    'storage',
    'bootstrap/cache',
    'public/build',
];

$included = [
    'app/',
    'routes/',
    'database/',
    'resources/js/',
];

function isExcluded(string $path, string $root, array $excluded): bool
{
    $rel = str_replace($root, '', $path);
    $rel = ltrim(str_replace('\\', '/', $rel), '/');

    foreach ($excluded as $dir) {
        $dir = trim(str_replace('\\', '/', $dir), '/');
        if ($rel === $dir || str_starts_with($rel, $dir . '/')) {
            return true;
        }
    }

    return false;
}

function isIncluded(string $path, string $root, array $included): bool
{
    $rel = str_replace($root, '', $path);
    $rel = ltrim(str_replace('\\', '/', $rel), '/');

    foreach ($included as $dir) {
        $dir = trim(str_replace('\\', '/', $dir), '/');
        if ($rel === $dir || str_starts_with($rel, $dir . '/')) {
            return true;
        }
    }

    return false;
}

function stripPhpComments(string $code): string
{
    $tokens = token_get_all($code);
    $out = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            $type = $token[0];
            $text = $token[1];

            if ($type === T_COMMENT || $type === T_DOC_COMMENT) {
                $newlines = substr_count($text, "\n");
                if ($newlines > 0) {
                    $out .= str_repeat("\n", $newlines);
                }
                continue;
            }

            $out .= $text;
            continue;
        }

        $out .= $token;
    }

    return $out;
}

function stripJsComments(string $code): string
{
    $len = strlen($code);
    $out = '';

    $state = 'normal';
    $escape = false;

    for ($i = 0; $i < $len; $i++) {
        $ch = $code[$i];
        $next = ($i + 1 < $len) ? $code[$i + 1] : '';

        if ($state === 'line_comment') {
            if ($ch === "\n") {
                $out .= "\n";
                $state = 'normal';
            }
            continue;
        }

        if ($state === 'block_comment') {
            if ($ch === "*" && $next === "/") {
                $state = 'normal';
                $i++;
                continue;
            }

            if ($ch === "\n") {
                $out .= "\n";
            }

            continue;
        }

        if ($state === 'sq') {
            $out .= $ch;
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === "\\") {
                $escape = true;
                continue;
            }
            if ($ch === "'") {
                $state = 'normal';
            }
            continue;
        }

        if ($state === 'dq') {
            $out .= $ch;
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === "\\") {
                $escape = true;
                continue;
            }
            if ($ch === '"') {
                $state = 'normal';
            }
            continue;
        }

        if ($state === 'tpl') {
            $out .= $ch;
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === "\\") {
                $escape = true;
                continue;
            }
            if ($ch === '`') {
                $state = 'normal';
            }
            continue;
        }

        if ($state === 'normal') {
            if ($ch === '/' && $next === '/') {
                $state = 'line_comment';
                $i++;
                continue;
            }

            if ($ch === '/' && $next === '*') {
                $state = 'block_comment';
                $i++;
                continue;
            }

            if ($ch === "'") {
                $state = 'sq';
                $out .= $ch;
                $escape = false;
                continue;
            }

            if ($ch === '"') {
                $state = 'dq';
                $out .= $ch;
                $escape = false;
                continue;
            }

            if ($ch === '`') {
                $state = 'tpl';
                $out .= $ch;
                $escape = false;
                continue;
            }

            $out .= $ch;
        }
    }

    return $out;
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$changed = 0;
$checked = 0;

foreach ($it as $file) {
    if (! $file instanceof SplFileInfo) {
        continue;
    }

    if (! $file->isFile()) {
        continue;
    }

    $path = $file->getPathname();

    if (isExcluded($path, $root, $excluded)) {
        continue;
    }

    if (! $all && ! isIncluded($path, $root, $included)) {
        continue;
    }

    $ext = strtolower((string) $file->getExtension());
    if ($ext !== 'php' && $ext !== 'js') {
        continue;
    }

    $checked++;

    $old = file_get_contents($path);
    if ($old === false) {
        continue;
    }

    $new = $old;
    if ($ext === 'php') {
        $new = stripPhpComments($old);
    } elseif ($ext === 'js') {
        $new = stripJsComments($old);
    }

    if ($new !== $old) {
        $changed++;
        $rel = ltrim(str_replace('\\', '/', str_replace($root, '', $path)), '/');
        echo ($dryRun ? 'DRY-RUN ' : 'APPLY ') . $rel . PHP_EOL;

        if (! $dryRun) {
            file_put_contents($path, $new);
        }
    }
}

echo 'Archivos revisados: ' . $checked . PHP_EOL;
echo 'Archivos con cambios: ' . $changed . PHP_EOL;
