<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

class TxtJsonlStore
{
    public function loadAll(string $absolutePath): array
    {
        if (! is_file($absolutePath)) {
            return [];
        }

        $lines = File::lines($absolutePath);

        $rows = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $decoded = json_decode($line, true);
            if (! is_array($decoded)) {
                continue;
            }

            $rows[] = $decoded;
        }

        return $rows;
    }

    public function saveAll(string $absolutePath, array $rows): void
    {
        File::ensureDirectoryExists(dirname($absolutePath));

        $tmpPath = $absolutePath . '.tmp';

        $content = '';
        foreach ($rows as $row) {
            $content .= json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
        }

        File::put($tmpPath, $content, true);
        File::move($tmpPath, $absolutePath);
    }
}
