<?php

namespace App\Dto;

class Person
{
    public function __construct(
        public string $name,
        public int $age
    ) {}


    public function toArray()
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
        ];
    }
}
