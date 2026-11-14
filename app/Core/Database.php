<?php

declare(strict_types=1);

namespace App\Core;

class Database
{
    private static ?Database $instance = null;

    private function __construct()
    {
        echo "Підключення до БД створено\n";
    }

    private function __clone(): void {}

    public function __wakeup(): void
    {
        throw new \LogicException(
            'Відновлення Singleton заборонено'
        );
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
