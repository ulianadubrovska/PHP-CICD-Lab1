<?php

declare(strict_types=1);

namespace App\Traits;

trait JsonExportTrait
{
    public function exportToJson(): string
    {
        $data = get_object_vars($this);

        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );
    }
}
