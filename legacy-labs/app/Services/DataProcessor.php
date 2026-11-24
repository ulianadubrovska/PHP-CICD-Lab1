<?php

declare(strict_types=1);

// ЗАДАЧА 1 (таблиця 3.2, №4)
// Перевірити, чи всі елементи масиву менші за K.
function areAllLessThanK(array $numbers, int $k): bool
{
    // Перебираємо кожне число масиву.
    foreach ($numbers as $number) {

        // Якщо хоча б одне число більше або дорівнює K,
        // умова не виконується.
        if ($number >= $k) {
            return false;
        }
    }

    // Якщо всі числа менші за K, повертаємо true.
    return true;
}

// ЗАДАЧА 2 (таблиця 3.3, №4)
// Поміняти місцями перше й останнє слово рядка.
function swapFirstLastWords(string $text): string
{
    // Видаляємо зайві пробіли з початку та кінця.
    $text = trim($text);

    // Розбиваємо рядок на масив слів.
    $words = preg_split('/\s+/u', $text);

    // Якщо слів менше двох, нічого не змінюємо.
    if (count($words) < 2) {
        return $text;
    }

    // Запам'ятовуємо перше слово.
    $first = $words[0];

    // Визначаємо індекс останнього слова.
    $lastIndex = count($words) - 1;

    // Ставимо останнє слово на місце першого.
    $words[0] = $words[$lastIndex];

    // Ставимо перше слово на місце останнього.
    $words[$lastIndex] = $first;

    // Об'єднуємо слова назад в один рядок.
    return implode(' ', $words);
}

// ЗАДАЧА 3 (таблиця 3.4, №3)
// Перемішати всі слова рядка у випадковому порядку.
function shuffleWords(string $text): string
{
    // Розділяємо текст на окремі слова.
    $words = preg_split('/\s+/u', trim($text));

    // Перемішуємо елементи масиву випадковим чином.
    shuffle($words);

    // Повертаємо перемішані слова у вигляді рядка.
    return implode(' ', $words);
}
