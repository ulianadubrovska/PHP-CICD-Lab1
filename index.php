<?php

declare(strict_types=1);

// Автозавантаження класів Composer.
require_once __DIR__.'/vendor/autoload.php';

// Алгоритмічні задачі лабораторної №3.
require_once __DIR__.'/app/Services/DataProcessor.php';

// Класи для лабораторних №3 та №4.
use App\Core\Database;
use App\Core\EntityFactory;
use App\Interfaces\ExportableInterface;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Services\FileLogger;
use App\Tasks\CompanyDirector;
use App\Tasks\DepartmentHead;
use App\Tasks\Product as StoreProduct;
use App\Tasks\Store;
use App\Tasks\Triangle;
use App\Tasks\WorkshopManager;
use Faker\Factory as FakerFactory;

// Захист тексту перед виведенням у HTML.
function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// ==========================================
// ЛАБОРАТОРНА РОБОТА №3
// ==========================================

// Створюємо логер і сервіс.
$logger = new ConsoleLogger;
$service = new EntityService($logger);

// ЗАВДАННЯ 2. РОЗРАХУНОК ВАРТОСТІ

// Футболка Oversize — 1200 грн.
$price = 1200.0;
$quantity = 3;

// Перехоплюємо повідомлення логера.
ob_start();

$total = $service->calculateTotal($price, $quantity);

$lab3Log = ob_get_clean();

// ЗАВДАННЯ 3. FAKER І ЗАМИКАННЯ

$faker = FakerFactory::create();

$prices = [];

// Генеруємо десять випадкових цін.
for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->randomFloat(2, 1000, 20000);
}

// Порогове значення для фільтрації.
$minThreshold = 5000;

// Залишаємо ціни понад 5000 грн.
$filteredPrices = array_filter(
    $prices,
    function (float $itemPrice) use ($minThreshold): bool {
        return $itemPrice > $minThreshold;
    }
);

// ЗАВДАННЯ 4. ОБРОБКА ВИНЯТКІВ

try {
    // Некоректна знижка 150%.
    $discountResult = $service->applyDiscount(
        1200.0,
        150.0
    );

    $discountMessage =
        "Ціна зі знижкою: $discountResult грн";

} catch (InvalidArgumentException $e) {
    $discountMessage =
        'Помилка: '.$e->getMessage();

} finally {
    $discountMessage .=
        ' Завершення спроби розрахунку.';
}

// ЗАВДАННЯ 5. ІНДИВІДУАЛЬНИЙ ВАРІАНТ №10

// Перевірка елементів масиву.
$numbers = [2, 4, 6, 8, 10];
$k = 15;

$checkResult = areAllLessThanK($numbers, $k);

// Обмін першого та останнього слова.
$sentence = 'Магазин продає стильний одяг';

$swappedSentence = swapFirstLastWords($sentence);

// Випадкове перемішування слів.
$originalText = 'Ми продаємо модний одяг аксесуари';

$shuffledText = shuffleWords($originalText);

// ==========================================
// ЛАБОРАТОРНА РОБОТА №4
// ==========================================

// ЗАВДАННЯ 1. SINGLETON

// Перехоплюємо повідомлення про створення БД.
ob_start();

$db1 = Database::getInstance();
$db2 = Database::getInstance();

$databaseMessage = ob_get_clean();

// Перевіряємо, чи це той самий об'єкт.
$isSameDatabase = $db1 === $db2;

// ЗАВДАННЯ 2. FACTORY ТА НАСЛІДУВАННЯ

// Створюємо фабрику об'єктів.
$factory = new EntityFactory;

// Створюємо модель товару — футболку.
$product = $factory->createEntity('product', [
    'id' => 'P001',
    'createdAt' => '2026-11-19',
    'name' => 'Футболка Oversize',
    'price' => 1200.0,
    'stock' => 12,
]);

// Створюємо користувача магазину.
$user = $factory->createEntity('user', [
    'id' => 'U001',
    'createdAt' => '2026-11-19',
    'name' => 'Олена',
    'email' => 'olena@example.com',
]);

// Викликаємо перевизначений метод getDetails().
$productDetails = $product->getDetails();
$userDetails = $user->getDetails();

// ЗАВДАННЯ 3. ІНТЕРФЕЙС І ТРЕЙТ

$productJson = '';

// Перевіряємо, чи товар підтримує експорт.
if ($product instanceof ExportableInterface) {
    $productJson = $product->exportToJson();
}

// ЗАВДАННЯ 4. DEPENDENCY INJECTION

// EntityService вже отримав ConsoleLogger
// через конструктор на початку скрипта.
ob_start();

$service->showEntity($product);
$service->showEntity($user);

$consoleResult = ob_get_clean();

// Змінюємо реалізацію логера на FileLogger.
$fileLogger = new FileLogger;

$fileService = new EntityService($fileLogger);

ob_start();

$fileService->showEntity($product);

$fileResult = ob_get_clean();

// ЗАВДАННЯ 5. КОМПОЗИЦІЯ PRODUCT І STORE

// Створюємо інтернет-магазин одягу.
$store = new Store(
    1,
    'Магазин одягу та аксесуарів'
);

// Додаємо перший товар.
$store->addProduct(
    new StoreProduct(
        1,
        'Футболка Oversize',
        1200.0
    )
);

// Додаємо другий товар.
$store->addProduct(
    new StoreProduct(
        2,
        'Шкіряний ремінь',
        700.0
    )
);

// Додаємо третій товар.
$store->addProduct(
    new StoreProduct(
        3,
        'Джинси Straight Fit',
        1850.0
    )
);

// Шукаємо футболку за назвою.
$foundProduct = $store->searchProduct(
    'Футболка Oversize'
);

// Видаляємо ремінь з ID 2.
$store->removeProduct(2);

// Отримуємо товари, які залишилися.
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

// Викликаємо методи calculateSalary().
$managerSalary = $manager->calculateSalary();
$headSalary = $head->calculateSalary();
$directorSalary = $director->calculateSalary();

// ЗАВДАННЯ 7. КЛАС TRIANGLE

// Створюємо трикутник зі сторонами 3, 4, 5.
$triangle = new Triangle(3.0, 4.0, 5.0);

$perimeter = $triangle->getPerimeter();
$area = $triangle->getArea();

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Лабораторні №3 та №4 — Магазин одягу</title>

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

    <h1>Вебпрограмування</h1>

    <p class="subtitle">
        Предметна галузь:
        Інтернет-магазин одягу та аксесуарів
    </p>


    <!-- ====================================
         ЛАБОРАТОРНА РОБОТА №3
         ==================================== -->

    <div class="lab-title">
        <h2>Лабораторна робота №3</h2>
        <p>Основи PHP: структури даних, функції</p>
    </div>


    <!-- ЗАВДАННЯ 1 -->
    <section>
        <?php require __DIR__.'/app/Models/EntityArray.php'; ?>
    </section>


    <!-- ЗАВДАННЯ 2 -->
    <section>
        <h2>Завдання 2. Користувацькі функції</h2>

        <p>Товар: Футболка Oversize</p>
        <p>Ціна однієї футболки: <?= h($price) ?> грн</p>
        <p>Кількість: <?= h($quantity) ?></p>

        <div class="result">
            <strong>Загальна вартість:</strong>
            <?= h($total) ?> грн
        </div>

        <p>
            Використано метод
            <code>EntityService::calculateTotal()</code>.
        </p>
    </section>


    <!-- ЗАВДАННЯ 3 -->
    <section>
        <h2>Завдання 3. Faker і замикання</h2>

        <h3>Згенеровані ціни одягу та аксесуарів</h3>

        <ul>
            <?php foreach ($prices as $itemPrice) { ?>
                <li><?= h($itemPrice) ?> грн</li>
            <?php } ?>
        </ul>

        <h3>Ціни понад 5000 грн</h3>

        <ul>
            <?php foreach ($filteredPrices as $itemPrice) { ?>
                <li><?= h($itemPrice) ?> грн</li>
            <?php } ?>
        </ul>
    </section>


    <!-- ЗАВДАННЯ 4 -->
    <section>
        <h2>Завдання 4. Обробка винятків</h2>

        <p>
            Перевірка некоректної знижки 150%
            на товар магазину.
        </p>

        <div class="result error">
            <?= h($discountMessage) ?>
        </div>
    </section>


    <!-- ЗАВДАННЯ 5 -->
    <section>
        <h2>Завдання 5. Індивідуальний варіант №10</h2>

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


    <hr class="divider">


    <!-- ====================================
         ЛАБОРАТОРНА РОБОТА №4
         ==================================== -->

    <div class="lab-title">
        <h2>Лабораторна робота №4</h2>

        <p>
            ООП у PHP: наслідування, трейти,
            контракти та патерни проєктування
        </p>
    </div>


    <!-- ЗАВДАННЯ 1. SINGLETON -->
    <section>
        <h2>Завдання 1. Singleton</h2>

        <p>
            Створення єдиного екземпляра класу
            <code>Database</code>.
        </p>

        <pre><?= h($databaseMessage) ?></pre>

        <div class="result">
            Обидві змінні посилаються на один об'єкт:

            <strong class="success">
                <?= $isSameDatabase ? 'Так' : 'Ні' ?>
            </strong>
        </div>
    </section>


    <!-- ЗАВДАННЯ 2. FACTORY -->
    <section>
        <h2>Завдання 2. Factory та наслідування</h2>

        <p>
            Фабрика створює об'єкти Product
            і User, що наслідують AbstractEntity.
        </p>

        <h3>Інформація про товар</h3>

        <div class="result">
            <?= h($productDetails) ?>
        </div>

        <h3>Інформація про користувача</h3>

        <div class="result">
            <?= h($userDetails) ?>
        </div>
    </section>


    <!-- ЗАВДАННЯ 3. JSON -->
    <section>
        <h2>Завдання 3. Інтерфейс і трейт</h2>

        <p>
            Експорт товару за допомогою
            ExportableInterface та JsonExportTrait.
        </p>

        <h3>JSON товару «Футболка Oversize»</h3>

        <pre><code><?= h($productJson) ?></code></pre>
    </section>


    <!-- ЗАВДАННЯ 4. DI -->
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
            Обидва логери реалізують один інтерфейс,
            тому їх можна змінювати без зміни
            логіки EntityService.
        </p>
    </section>


    <!-- ЗАВДАННЯ 5. КОМПОЗИЦІЯ -->
    <section>
        <h2>Завдання 5. Композиція Product і Store</h2>

        <p>
            У магазин додано футболку, шкіряний
            ремінь і джинси. Ремінь із ID 2
            видалено.
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


    <!-- ЗАВДАННЯ 6. ІЄРАРХІЯ -->
    <section>
        <h2>Завдання 6. Ієрархія працівників</h2>

        <p>
            Демонстрація наслідування класів Worker,
            WorkshopManager, DepartmentHead та
            CompanyDirector.
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


    <!-- ЗАВДАННЯ 7. TRIANGLE -->
    <section>
        <h2>Завдання 7. Клас Triangle</h2>

        <p>
            Обчислення периметра та площі трикутника
            за формулою Герона.
        </p>

        <p>Сторони трикутника: 3, 4, 5</p>

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


    <!-- ПІДСУМОК -->
    <section>
        <h2>Результат виконання</h2>

        <p class="success">
            Продемонстровано лабораторні роботи
            №3 та №4 для інтернет-магазину
            одягу та аксесуарів.
        </p>
    </section>

</main>
</body>
</html>
