<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;

trait HandlesCsv
{
    protected function streamCsvDownload(string $fileName, array $headerRow, callable $rowWriter)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($headerRow, $rowWriter) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $headerRow);
            $rowWriter($file);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function parseCsvFile(UploadedFile $uploadedFile): array
    {
        $handle = fopen($uploadedFile->getRealPath(), 'r');

        if (!$handle) {
            return ['headers' => [], 'rows' => []];
        }

        $firstBytes = fread($handle, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            fclose($handle);

            return ['headers' => [], 'rows' => []];
        }

        $headers = array_map(fn ($h) => $this->normalizeCsvHeader($h), $headerRow);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $key) {
                $row[$key] = trim((string) ($data[$index] ?? ''));
            }

            $rows[] = $row;
        }

        fclose($handle);

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected function normalizeCsvHeader(string $header): string
    {
        $header = trim($header);
        $header = preg_replace('/\s+/', '_', $header);

        return strtolower($header);
    }

    protected function csvValue(array $row, string $key, $default = '')
    {
        if (isset($row[$key]) && $row[$key] !== '') {
            return $row[$key];
        }

        $alt = $this->normalizeCsvHeader($key);
        if (isset($row[$alt]) && $row[$alt] !== '') {
            return $row[$alt];
        }

        return $default;
    }

    protected function normalizeStatusValue(?string $value, int $default = 1): int
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return $default;
        }

        if (in_array($value, ['1', 'active', 'yes', 'true'], true)) {
            return 1;
        }

        if (in_array($value, ['0', 'inactive', 'no', 'false'], true)) {
            return 0;
        }

        return $default;
    }
}
