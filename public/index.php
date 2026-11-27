<?php

date_default_timezone_set('Europe/Moscow');

$isLocal = PHP_SAPI === 'cli-server';

ini_set('display_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../src/functions.php';

$appName = 'Заявки сотрудников';
$appVersion = '0.2';
$practiceNumber = 4;

$employees = [];
$loadError = '';

try {
    $employees = loadEmployees(__DIR__ . '/../dataset/employees.csv');
} catch (RuntimeException $exception) {
    $loadError = $exception->getMessage();
}

$employeeCount = count($employees);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapeHtml($appName) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <header>
        <h1><?= escapeHtml($appName) ?></h1>
        <p>Практическая работа №<?= $practiceNumber ?></p>
        <p>Версия приложения: <?= escapeHtml($appVersion) ?></p>
        <p>Текущая дата: <?= date('d.m.Y H:i') ?></p>
    </header>

    <main>
        <section>
            <h2>Сотрудники</h2>

            <?php if ($loadError !== ''): ?>
                <p class="error"><?= escapeHtml($loadError) ?></p>
            <?php elseif ($employees === []): ?>
                <p>Список сотрудников пуст.</p>
            <?php else: ?>
                <p>Всего сотрудников: <?= $employeeCount ?></p>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Отдел</th>
                                <th scope="col">Стаж, лет</th>
                                <th scope="col">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?= $employee['id'] ?></td>
                                    <td><?= escapeHtml($employee['name']) ?></td>
                                    <td><?= escapeHtml($employee['department']) ?></td>
                                    <td><?= $employee['experience'] ?></td>
                                    <td><?= escapeHtml($employee['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>