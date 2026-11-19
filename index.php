<?php

// Увімкнення строгої типізації PHP.
declare(strict_types=1);

// Підключаємо необхідні класи.
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use Faker\Factory as FakerFactory;

// Автоматичне завантаження класів через Composer.
require_once __DIR__.'/vendor/autoload.php';

// Підключаємо функції алгоритмічних задач варіанта №10.
require_once __DIR__.'/app/Services/DataProcessor.php';

// Допоміжна функція для безпечного виведення даних у HTML.
function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Створюємо логер і сервіс для розрахунку вартості.
// EntityService використовує Dependency Injection.
$logger = new ConsoleLogger;
$service = new EntityService($logger);

// ==========================================
// ЗАВДАННЯ 2. КОРИСТУВАЦЬКІ ФУНКЦІЇ
// ==========================================

// Ціна однієї футболки Oversize.
$price = 1200.0;

// Кількість футболок.
$quantity = 3;

// Перехоплюємо повідомлення, яке виводить логер.
ob_start();

// Викликаємо метод розрахунку загальної вартості.
$total = $service->calculateTotal($price, $quantity);

// Зберігаємо повідомлення логера.
$lab3Log = ob_get_clean();

// ==========================================
// ЗАВДАННЯ 3. FAKER І ЗАМИКАННЯ
// ==========================================

// Створюємо генератор тестових даних FakerPHP.
$faker = FakerFactory::create();

// Масив для зберігання випадкових цін.
$prices = [];

// Генеруємо 10 випадкових цін одягу та аксесуарів.
for ($i = 0; $i < 10; $i++) {

    // Два знаки після коми, діапазон 1000–20000 грн.
    $prices[] = $faker->randomFloat(2, 1000, 20000);
}

// Мінімальна ціна для фільтрації.
$minThreshold = 5000;

// Відбираємо лише ціни, що перевищують 5000 грн.
$filteredPrices = array_filter(
    $prices,
    function (float $itemPrice) use ($minThreshold): bool {

        // true — залишаємо ціну в результаті.
        // false — виключаємо з результату.
        return $itemPrice > $minThreshold;
    }
);

// ==========================================
// ЗАВДАННЯ 4. ОБРОБКА ВИНЯТКІВ
// ==========================================

try {

    // Перевіряємо некоректну знижку 150%.
    // Допустимий діапазон знижки — від 0 до 100%.
    $discountResult = $service->applyDiscount(
        1200.0,
        150.0
    );

    // Виконується лише якщо винятку не було.
    $discountMessage =
        "Ціна зі знижкою: $discountResult грн";

} catch (InvalidArgumentException $e) {

    // Перехоплюємо виняток неправильного аргументу.
    $discountMessage =
        'Помилка: '.$e->getMessage();

} finally {

    // Виконується незалежно від виникнення винятку.
    $discountMessage .=
        ' Завершення спроби розрахунку.';
}

// ==========================================
// ЗАВДАННЯ 5. ІНДИВІДУАЛЬНИЙ ВАРІАНТ №10
// ==========================================

// ЗАДАЧА 1. Перевірка всіх елементів масиву.
// Усі числа повинні бути меншими за K.
$numbers = [2, 4, 6, 8, 10];

$k = 15;

// Результат true або false.
$checkResult = areAllLessThanK($numbers, $k);

// ЗАДАЧА 2. Обмін першого й останнього слова.
$sentence = 'Магазин продає стильний одяг';

$swappedSentence = swapFirstLastWords($sentence);

// ЗАДАЧА 3. Перемішування слів у випадковому порядку.
$originalText = 'Ми продаємо модний одяг аксесуари';

$shuffledText = shuffleWords($originalText);

?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <!-- Кодування для української мови -->
    <meta charset="UTF-8">

    <!-- Адаптація сторінки до розмірів екрана -->
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <!-- Назва вкладки браузера -->
    <title>Лабораторна №3 — Магазин одягу та аксесуарів</title>

    <style>
        /* Основні налаштування сторінки */
        body {
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            margin: 40px;
            line-height: 1.6;
            color: #263238;
        }

        /* Контейнер для основного вмісту */
        main {
            max-width: 900px;
            margin: auto;
        }

        /* Білі блоки з результатами кожного завдання */
        section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        /* Головний заголовок */
        h1 {
            color: #1d3557;
        }

        /* Заголовки завдань */
        h2 {
            color: #2563eb;
        }

        h3 {
            color: #334155;
        }

        li {
            margin-bottom: 5px;
        }

        /* Додатковий текст під назвою */
        .subtitle {
            color: #64748b;
        }

        /* Темний заголовок лабораторної */
        .lab-title {
            background: #1d3557;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0 20px;
        }

        .lab-title h2 {
            color: white;
            margin: 0 0 10px;
        }

        /* Виділення результатів */
        .result {
            background: #eef6ff;
            border-left: 4px solid #2563eb;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
        }

        /* Повідомлення про помилку */
        .error {
            color: #b91c1c;
        }

        /* Відображення повідомлення логера */
        pre {
            background: #172033;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap;
        }

        code {
            font-family: Consolas, monospace;
        }
    </style>

</head>

<body>

<main>

    <!-- Загальна інформація про лабораторну -->
    <h1>Вебпрограмування</h1>

    <p class="subtitle">
        Предметна галузь:
        Інтернет-магазин одягу та аксесуарів
    </p>

    <div class="lab-title">

        <h2>Лабораторна робота №3</h2>

        <p>Основи PHP: структури даних, функції</p>

    </div>


    <!-- ======================================
         ЗАВДАННЯ 1. РОБОТА З МАСИВАМИ
         ====================================== -->

    <section>

        <!-- У цьому файлі вже міститься заголовок
             першого завдання та HTML-списки. -->
        <?php require __DIR__.'/app/Models/EntityArray.php'; ?>

    </section>


    <!-- ======================================
         ЗАВДАННЯ 2. РОЗРАХУНОК ВАРТОСТІ
         ====================================== -->

    <section>

        <h2>Завдання 2. Користувацькі функції</h2>

        <p>Товар: Футболка Oversize</p>

        <p>
            Ціна однієї футболки:
            <?= h($price) ?> грн
        </p>

        <p>
            Кількість товарів:
            <?= h($quantity) ?>
        </p>

        <div class="result">

            <strong>Загальна вартість:</strong>

            <?= h($total) ?> грн

        </div>

        <p>
            Розрахунок виконано методом
            <code>EntityService::calculateTotal()</code>.
        </p>

    </section>


    <!-- ======================================
         ЗАВДАННЯ 3. FAKER І ЗАМИКАННЯ
         ====================================== -->

    <section>

        <h2>Завдання 3. Faker і замикання</h2>

        <h3>Згенеровані ціни одягу та аксесуарів</h3>

        <ul>
            <!-- Виведення десяти випадкових цін -->
            <?php foreach ($prices as $itemPrice) { ?>

                <li><?= h($itemPrice) ?> грн</li>

            <?php } ?>
        </ul>

        <h3>Ціни понад 5000 грн</h3>

        <ul>
            <!-- Виведення цін після фільтрації -->
            <?php foreach ($filteredPrices as $itemPrice) { ?>

                <li><?= h($itemPrice) ?> грн</li>

            <?php } ?>
        </ul>

    </section>


    <!-- ======================================
         ЗАВДАННЯ 4. ОБРОБКА ВИНЯТКІВ
         ====================================== -->

    <section>

        <h2>Завдання 4. Обробка винятків</h2>

        <p>
            Перевіряємо застосування некоректної
            знижки 150% до товару магазину.
        </p>

        <div class="result error">

            <?= h($discountMessage) ?>

        </div>

    </section>


    <!-- ======================================
         ЗАВДАННЯ 5. ВАРІАНТ №10
         ====================================== -->

    <section>

        <h2>Завдання 5. Індивідуальний варіант №10</h2>


        <!-- Перша алгоритмічна задача -->
        <h3>1. Перевірка елементів масиву</h3>

        <p>
            Початковий масив:
            <?= h(implode(', ', $numbers)) ?>
        </p>

        <p>Число K: <?= h($k) ?></p>

        <div class="result">

            Результат:
            <?= $checkResult ? 'true' : 'false' ?>

        </div>


        <!-- Друга алгоритмічна задача -->
        <h3>2. Обмін першого й останнього слова</h3>

        <p>
            Початковий рядок:
            <?= h($sentence) ?>
        </p>

        <div class="result">

            Результат:
            <?= h($swappedSentence) ?>

        </div>


        <!-- Третя алгоритмічна задача -->
        <h3>3. Перемішування слів рядка</h3>

        <p>
            Початковий рядок:
            <?= h($originalText) ?>
        </p>

        <div class="result">

            Результат:
            <?= h($shuffledText) ?>

        </div>

    </section>


    <!-- Завершення сторінки -->
    <section>

        <h2>Результат виконання</h2>

        <p>
            Продемонстровано роботу з масивами,
            користувацькими функціями,
            бібліотекою FakerPHP, замиканнями,
            винятками та алгоритмічними задачами
            індивідуального варіанта №10.
        </p>

    </section>

</main>

</body>
</html>
