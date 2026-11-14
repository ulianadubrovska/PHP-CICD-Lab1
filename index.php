<?php

// Увімкнення строгої типізації
declare(strict_types=1);

// Автозавантаження Composer
require_once __DIR__.'/vendor/autoload.php';

// Функції алгоритмічних задач лабораторної №3
require_once __DIR__.'/app/Services/DataProcessor.php';

// Бібліотека Faker
use App\Core\Database;
// Класи лабораторної №4
use App\Core\EntityFactory;
use App\Interfaces\ExportableInterface;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Services\FileLogger;
use App\Tasks\CompanyDirector;
// Індивідуальний варіант №10
use App\Tasks\DepartmentHead;
use App\Tasks\Product as StoreProduct;
use App\Tasks\Store;
use App\Tasks\Triangle;
use App\Tasks\WorkshopManager;
use Faker\Factory as FakerFactory;

// Допоміжна функція для безпечного виведення в HTML
function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// ==========================================
// ЛАБОРАТОРНА №3
// ==========================================

// Створюємо логер і сервіс
$logger = new ConsoleLogger;
$service = new EntityService($logger);

// Зберігаємо повідомлення логера в змінну,
// щоб вивести їх усередині HTML-сторінки
ob_start();

// ЗАВДАННЯ 2. РОЗРАХУНОК ВАРТОСТІ
$total = $service->calculateTotal(2500.0, 3);

$lab3Log = ob_get_clean();

// ЗАВДАННЯ 3. FAKER І ЗАМИКАННЯ
$faker = FakerFactory::create();

$prices = [];

for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->randomFloat(2, 1000, 20000);
}

$minThreshold = 5000;

$filteredPrices = array_filter(
    $prices,
    function (float $price) use ($minThreshold): bool {
        return $price > $minThreshold;
    }
);

// ЗАВДАННЯ 4. ОБРОБКА ВИНЯТКІВ
try {
    $discountResult = $service->applyDiscount(
        1000.0,
        150.0
    );

    $discountMessage =
        "Ціна зі знижкою: $discountResult грн";

} catch (Exception $e) {
    $discountMessage =
        'Помилка: '.$e->getMessage();

} finally {
    $discountMessage .=
        ' Завершення спроби розрахунку.';
}

// ЗАВДАННЯ 5. ІНДИВІДУАЛЬНИЙ ВАРІАНТ №10

// Перевірка елементів масиву
$numbers = [2, 4, 6, 8, 10];
$k = 15;
$checkResult = areAllLessThanK($numbers, $k);

// Обмін першого й останнього слова
$sentence = 'Магазин продає якісні товари';
$swappedSentence = swapFirstLastWords($sentence);

// Перемішування слів
$originalText = 'Ми створюємо сучасний інтернет магазин';
$shuffledText = shuffleWords($originalText);

// ==========================================
// ЛАБОРАТОРНА №4
// ==========================================

// ЗАВДАННЯ 1. SINGLETON
ob_start();

$db1 = Database::getInstance();
$db2 = Database::getInstance();

$databaseMessage = ob_get_clean();

// Перевіряємо, чи це один об'єкт
$isSameDatabase = $db1 === $db2;

// ЗАВДАННЯ 2. FACTORY ТА НАСЛІДУВАННЯ
$factory = new EntityFactory;

// Створення товару через фабрику
$product = $factory->createEntity('product', [
    'id' => 'P001',
    'createdAt' => '2026-11-14',
    'name' => 'Ноутбук Lenovo',
    'price' => 25000.0,
    'stock' => 5,
]);

// Створення користувача через фабрику
$user = $factory->createEntity('user', [
    'id' => 'U001',
    'createdAt' => '2026-11-14',
    'name' => 'Олена',
    'email' => 'olena@example.com',
]);

$productDetails = $product->getDetails();
$userDetails = $user->getDetails();

// ЗАВДАННЯ 3. JSON EXPORT
$productJson = '';

if ($product instanceof ExportableInterface) {
    $productJson = $product->exportToJson();
}

// ЗАВДАННЯ 4. DEPENDENCY INJECTION

// Демонструємо роботу ConsoleLogger
ob_start();

$service->showEntity($product);
$service->showEntity($user);

$consoleResult = ob_get_clean();

// Створюємо іншу реалізацію логера
$fileLogger = new FileLogger;

// Передаємо її в той самий клас сервісу
$fileService = new EntityService($fileLogger);

ob_start();

$fileService->showEntity($product);

$fileResult = ob_get_clean();

// ЗАВДАННЯ 5. КОМПОЗИЦІЯ КЛАСІВ

$store = new Store(1, 'Online Store');

// Додаємо два товари
$store->addProduct(
    new StoreProduct(1, 'Ноутбук', 25000.0)
);

$store->addProduct(
    new StoreProduct(2, 'Мишка', 700.0)
);

// Шукаємо товар
$foundProduct = $store->searchProduct('Ноутбук');

// Видаляємо товар з ID 2
$store->removeProduct(2);

// Отримуємо залишок товарів
$storeProducts = $store->getProducts();

// ЗАВДАННЯ 6. ІЄРАРХІЯ ПРАЦІВНИКІВ

$manager = new WorkshopManager(
    'Іван',
    20000.0
);

$head = new DepartmentHead(
    'Марія',
    20000.0
);

$director = new CompanyDirector(
    'Петро',
    20000.0
);

$managerSalary = $manager->calculateSalary();
$headSalary = $head->calculateSalary();
$directorSalary = $director->calculateSalary();

// ЗАВДАННЯ 7. КЛАС TRIANGLE

$triangle = new Triangle(
    3.0,
    4.0,
    5.0
);

$perimeter = $triangle->getPerimeter();
$area = $triangle->getArea();

?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Лабораторні роботи №3 та №4</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            margin: 40px;
            line-height: 1.6;
            color: #263238;
        }

        main {
            max-width: 900px;
            margin: auto;
        }

        section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        h1 {
            color: #1d3557;
        }

        h2 {
            color: #2563eb;
        }

        h3 {
            color: #334155;
        }

        li {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #64748b;
        }

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

        .lab-title p {
            margin: 0;
        }

        .result {
            background: #eef6ff;
            border-left: 4px solid #2563eb;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success {
            color: #15803d;
            font-weight: bold;
        }

        .error {
            color: #b91c1c;
        }

        pre {
            background: #172033;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        code {
            font-family: Consolas, monospace;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dbe2ea;
        }

        th {
            background: #eaf1ff;
        }

        .divider {
            border: 0;
            border-top: 2px solid #dbe2ea;
            margin: 40px 0;
        }
    </style>
</head>

<body>

<main>

    <h1>Веб-програмування</h1>

    <p class="subtitle">
        Предметна галузь: Інтернет-магазин Online Store
    </p>

    <!-- ======================================
         ЛАБОРАТОРНА РОБОТА №3
         ====================================== -->

    <div class="lab-title">
        <h2>Лабораторна робота №3</h2>
        <p>Основи PHP: структури даних, функції</p>
    </div>


    <!-- ЗАВДАННЯ 1 -->
    <section>
        <h2>Завдання 1. Робота з масивами</h2>

        <?php
        require __DIR__.'/app/Models/EntityArray.php';
?>
    </section>


    <!-- ЗАВДАННЯ 2 -->
    <section>
        <h2>Завдання 2. Користувацькі функції</h2>

        <p>
            Ціна одного товару: 2500 грн
        </p>

        <p>Кількість: 3</p>

        <div class="result">
            <strong>Загальна вартість:</strong>
            <?= h($total) ?> грн
        </div>

        <p>
            Розрахунок виконаний методом
            <code>EntityService::calculateTotal()</code>.
        </p>
    </section>


    <!-- ЗАВДАННЯ 3 -->
    <section>
        <h2>Завдання 3. Faker і замикання</h2>

        <h3>Згенеровані ціни</h3>

        <ul>
            <?php foreach ($prices as $price) { ?>
                <li><?= h($price) ?> грн</li>
            <?php } ?>
        </ul>

        <h3>Ціни понад 5000 грн</h3>

        <ul>
            <?php foreach ($filteredPrices as $price) { ?>
                <li><?= h($price) ?> грн</li>
            <?php } ?>
        </ul>
    </section>


    <!-- ЗАВДАННЯ 4 -->
    <section>
        <h2>Завдання 4. Обробка винятків</h2>

        <p>
            Перевіряємо неправильну знижку 150%.
        </p>

        <div class="result error">
            <?= h($discountMessage) ?>
        </div>
    </section>


    <!-- ЗАВДАННЯ 5 -->
    <section>
        <h2>Завдання 5. Варіант №10</h2>

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

        <h3>2. Обмін першого й останнього слова</h3>

        <p>
            Початковий рядок:
            <?= h($sentence) ?>
        </p>

        <div class="result">
            Результат:
            <?= h($swappedSentence) ?>
        </div>

        <h3>3. Перемішування слів</h3>

        <p>
            Початковий рядок:
            <?= h($originalText) ?>
        </p>

        <div class="result">
            Результат:
            <?= h($shuffledText) ?>
        </div>
    </section>


    <hr class="divider">


    <!-- ======================================
         ЛАБОРАТОРНА РОБОТА №4
         ====================================== -->

    <div class="lab-title">
        <h2>Лабораторна робота №4</h2>

        <p>
            ООП в PHP: наслідування, трейти,
            контракти та патерни проєктування
        </p>
    </div>


    <!-- SINGLETON -->
    <section>
        <h2>Завдання 1. Singleton</h2>

        <p>
            Патерн Singleton гарантує використання
            єдиного екземпляра класу Database.
        </p>

        <pre><?= h($databaseMessage) ?></pre>

        <div class="result">
            Обидві змінні посилаються на один об'єкт:

            <span class="success">
                <?= $isSameDatabase ? 'Так' : 'Ні' ?>
            </span>
        </div>
    </section>


    <!-- FACTORY І НАСЛІДУВАННЯ -->
    <section>
        <h2>Завдання 2. Factory та наслідування</h2>

        <p>
            Об'єкти Product та User створюються
            через EntityFactory.
        </p>

        <h3>Інформація про товар</h3>

        <div class="result">
            <?= h($productDetails) ?>
        </div>

        <h3>Інформація про користувача</h3>

        <div class="result">
            <?= h($userDetails) ?>
        </div>

        <p>
            Обидва класи наслідують AbstractEntity
            та реалізують метод getDetails().
        </p>
    </section>


    <!-- ІНТЕРФЕЙС І ТРЕЙТ -->
    <section>
        <h2>Завдання 3. JSON Export</h2>

        <p>
            Використовуємо ExportableInterface
            та JsonExportTrait для експорту товару.
        </p>

        <pre><code><?= h($productJson) ?></code></pre>
    </section>


    <!-- DEPENDENCY INJECTION -->
    <section>
        <h2>Завдання 4. Dependency Injection</h2>

        <p>
            EntityService отримує LoggerInterface
            через конструктор.
        </p>

        <h3>ConsoleLogger</h3>

        <pre><?= h($consoleResult) ?></pre>

        <h3>FileLogger</h3>

        <pre><?= h($fileResult) ?></pre>

        <p>
            Ми можемо змінювати реалізацію логера
            без зміни бізнес-логіки сервісу.
        </p>
    </section>


    <!-- КОМПОЗИЦІЯ -->
    <section>
        <h2>Завдання 5. Композиція Product і Store</h2>

        <p>
            У магазин додано два товари.
            Товар із ID 2 видалено.
        </p>

        <h3>Пошук товару</h3>

        <div class="result">
            <?php if ($foundProduct !== null) { ?>
                Знайдено:
                <?= h($foundProduct->name) ?>

                — <?= h($foundProduct->price) ?> грн
            <?php } else { ?>
                Товар не знайдено.
            <?php } ?>
        </div>

        <h3>Товари, що залишилися</h3>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Ціна</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($storeProducts as $item) { ?>
                    <tr>
                        <td><?= h($item->id) ?></td>
                        <td><?= h($item->name) ?></td>
                        <td><?= h($item->price) ?> грн</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <p>
            Кількість товарів:
            <strong><?= count($storeProducts) ?></strong>
        </p>
    </section>


    <!-- ІЄРАРХІЯ ПРАЦІВНИКІВ -->
    <section>
        <h2>Завдання 6. Ієрархія працівників</h2>

        <p>
            Демонстрація багаторівневого наслідування
            та перевизначення calculateSalary().
        </p>

        <table>
            <thead>
                <tr>
                    <th>Посада</th>
                    <th>Зарплата</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Керівник цеху</td>
                    <td><?= h($managerSalary) ?> грн</td>
                </tr>

                <tr>
                    <td>Начальник відділу</td>
                    <td><?= h($headSalary) ?> грн</td>
                </tr>

                <tr>
                    <td>Керівник підприємства</td>
                    <td><?= h($directorSalary) ?> грн</td>
                </tr>
            </tbody>
        </table>
    </section>


    <!-- TRIANGLE -->
    <section>
        <h2>Завдання 7. Клас Triangle</h2>

        <p>
            Обчислення периметра та площі
            за формулою Герона.
        </p>

        <p>Сторони: 3, 4, 5</p>

        <div class="result">
            <p>
                <strong>Периметр:</strong>
                <?= h($perimeter) ?>
            </p>

            <p>
                <strong>Площа:</strong>
                <?= h($area) ?>
            </p>
        </div>
    </section>


    <section>
        <h2>Результат виконання</h2>

        <p class="success">
            Демонстрацію лабораторних робіт
            №3 та №4 завершено.
        </p>
    </section>

</main>

</body>
</html>
