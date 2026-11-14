<?php

declare(strict_types=1);

namespace App\Models;

class User extends AbstractEntity
{
    public function __construct(
        string $id,
        string $createdAt,
        private string $name,
        private string $email
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Користувач: {$this->name}, email: {$this->email}";
    }
}
