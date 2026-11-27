<?php

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function normalizeEmployee(array $row): array
{
    return [
        'id' => (int) $row[0],
        'name' => trim($row[1]),
        'department' => trim($row[2]),
        'experience' => (int) $row[3],
        'email' => trim($row[4]),
    ];
}

function loadEmployees(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('Файл сотрудников отсутствует или недоступен.');
    }

    $file = fopen($path, 'r');

    if ($file === false) {
        throw new RuntimeException('Не удалось открыть файл сотрудников.');
    }

    try {
        if (fread($file, 3) !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $headers = fgetcsv($file, 0, ';', '"', '');

        $expectedHeaders = [
            'id',
            'name',
            'department',
            'experience',
            'email',
        ];

        if ($headers !== $expectedHeaders) {
            throw new RuntimeException('Неверные заголовки в файле сотрудников.');
        }

        $employees = [];

        while (($row = fgetcsv($file, 0, ';', '"', '')) !== false) {
            if ($row === [null]) {
                continue;
            }

            if (count($row) !== count($expectedHeaders)) {
                throw new RuntimeException('Неверное количество полей в строке CSV.');
            }

            $employees[] = normalizeEmployee($row);
        }

        return $employees;
    } finally {
        fclose($file);
    }
}