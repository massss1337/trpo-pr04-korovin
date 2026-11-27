<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../src/functions.php';

$employees = loadEmployees(__DIR__ . '/../dataset/employees.csv');

$departments = array_values(
    array_unique(array_column($employees, 'department'))
);

$cases = json_decode(
    file_get_contents(__DIR__ . '/../dataset/form_cases.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);

$failed = 0;

foreach ($cases as $case) {
    $input = [
        'name' => normalizeText($case['name'] ?? ''),
        'department' => $departments[0] ?? '',
        'experience' => normalizeText($case['experience'] ?? ''),
        'email' => 'test@example.test',
    ];

    $errors = validateInput($input, $departments);

    $actualValid = $errors === [];
    $expectedValid = $case['valid'];

    $passed = $actualValid === $expectedValid;

    if (!$passed) {
        $failed++;
    }

    $result = $passed ? 'ПРОЙДЕН' : 'ОШИБКА';

    echo $case['case'] . ': ' . $result . PHP_EOL;
}

echo 'Всего: ' . count($cases) . '. Ошибок: ' . $failed . '.' . PHP_EOL;

exit($failed === 0 ? 0 : 1);