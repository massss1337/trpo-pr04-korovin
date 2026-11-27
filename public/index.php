<?php

date_default_timezone_set('Europe/Moscow');

$isLocal = PHP_SAPI === 'cli-server';

ini_set('display_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../src/functions.php';

$appName = 'Заявки сотрудников';
$appVersion = '0.1';
$practiceNumber = 4;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapeHtml($appName) ?></title>
</head>
<body>
    <header>
        <h1><?= escapeHtml($appName) ?></h1>
        <p>Практическая работа №<?= $practiceNumber ?></p>
    </header>

    <main>
        <p>Версия приложения: <?= escapeHtml($appVersion) ?></p>
        <p>Текущая дата: <?= date('d.m.Y H:i') ?></p>
    </main>
</body>
</html>