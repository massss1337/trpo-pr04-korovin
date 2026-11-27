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

function normalizeText($value): string
{
    return is_scalar($value) ? trim((string) $value) : '';
}

function validateInput(array $input, array $departments): array
{
    $errors = [];

    if ($input['name'] === '') {
        $errors['name'] = 'Введите имя.';
    } elseif (
        mb_strlen($input['name'], 'UTF-8') < 2 ||
        mb_strlen($input['name'], 'UTF-8') > 100
    ) {
        $errors['name'] = 'Имя должно содержать от 2 до 100 символов.';
    }

    if (!in_array($input['department'], $departments, true)) {
        $errors['department'] = 'Выберите отдел из списка.';
    }

    $experience = filter_var(
        $input['experience'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0, 'max_range' => 60]]
    );

    if ($experience === false) {
        $errors['experience'] = 'Стаж должен быть целым числом от 0 до 60 лет.';
    }

    if ($input['email'] === '') {
        $errors['email'] = 'Введите email.';
    } elseif (
        mb_strlen($input['email'], 'UTF-8') > 254 ||
        filter_var($input['email'], FILTER_VALIDATE_EMAIL) === false
    ) {
        $errors['email'] = 'Введите корректный email.';
    }

    return $errors;
}

function filterEmployees(array $employees, string $department): array
{
    $result = [];

    foreach ($employees as $employee) {
        if ($department === '' || $employee['department'] === $department) {
            $result[] = $employee;
        }
    }

    return $result;
}