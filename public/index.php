<?php

date_default_timezone_set('Europe/Moscow');

$isLocal = PHP_SAPI === 'cli-server';

ini_set('display_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../src/functions.php';

$appName = 'Заявки сотрудников';
$appVersion = '0.3';
$practiceNumber = 4;

$employees = [];
$loadError = '';

try {
    $employees = loadEmployees(__DIR__ . '/../dataset/employees.csv');
} catch (RuntimeException $exception) {
    $loadError = $exception->getMessage();
}

$employeeCount = count($employees);

$departments = array_values(
    array_unique(array_column($employees, 'department'))
);

sort($departments);

$input = [
    'name' => normalizeText($_POST['name'] ?? ''),
    'department' => normalizeText($_POST['department'] ?? ''),
    'experience' => normalizeText($_POST['experience'] ?? ''),
    'email' => normalizeText($_POST['email'] ?? ''),
];

$errors = [];
$success = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $errors = validateInput($input, $departments);
    $success = $errors === [];
}

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
            <section>
            <h2>Заявка сотрудника</h2>

            <?php if ($loadError !== '' || $departments === []): ?>
                <p>Отправка заявки временно недоступна.</p>
            <?php else: ?>

                <?php if ($errors !== []): ?>
                    <div class="error" role="alert">
                        <p>Проверьте введённые данные:</p>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= escapeHtml($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success" role="status">
                        <h3>Заявка успешно обработана</h3>
                        <p>
                            Имя:
                            <?= escapeHtml($input['name']) ?>
                        </p>
                        <p>
                            Отдел:
                            <?= escapeHtml($input['department']) ?>
                        </p>
                        <p>
                            Стаж:
                            <?= escapeHtml($input['experience']) ?> лет
                        </p>
                        <p>
                            Email:
                            <?= escapeHtml($input['email']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <form method="post" action="/" class="request-form" novalidate>
                    <div class="form-row">
                        <label for="name">Имя</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= escapeHtml($input['name']) ?>"
                            minlength="2"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <label for="department">Отдел</label>
                        <select id="department" name="department" required>
                            <option value="">Выберите отдел</option>

                            <?php foreach ($departments as $department): ?>
                                <option
                                    value="<?= escapeHtml($department) ?>"
                                    <?= $input['department'] === $department
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= escapeHtml($department) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="experience">Стаж, лет</label>
                        <input
                            type="number"
                            id="experience"
                            name="experience"
                            value="<?= escapeHtml($input['experience']) ?>"
                            min="0"
                            max="60"
                            step="1"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= escapeHtml($input['email']) ?>"
                            maxlength="254"
                            required
                        >
                    </div>

                    <button type="submit">Отправить заявку</button>
                </form>

            <?php endif; ?>
        </section>
    </main>
</body>
</html>