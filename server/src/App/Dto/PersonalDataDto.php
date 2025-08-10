<?php

declare(strict_types=1);

namespace App\Dto;

class PersonalDataDto
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
    ) {
    }
}
