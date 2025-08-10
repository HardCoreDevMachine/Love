<?php

declare(strict_types=1);

namespace App\Helper;

use App\Exception\ValidationException;

final class ValidatorHelper
{
    /**
     * @param array $rules
     */
    public function __construct(
        private array $rules
    ) {
    }

    /**
     * @param array $data
     * @return void
     */
    public function arrayValidate(array $data): void
    {
        foreach ($this->rules as $name => $type) {
            // Проверка наличия поля
            if (!array_key_exists($name, $data)) {
                throw new ValidationException('Поле ' . $name . ' отсутствует');
            }

            // Проверка типа
            if (gettype($data[$name]) !== $type) {
                throw new ValidationException('Поле ' . $name . ' должно быть типа ' . $type . ' А на самом деле - ' . gettype($data[$name])  );
            }

            // Проверка на пустое значение (если нужно)
            if ($data[$name] === '') {
                throw new ValidationException('Поле ' . $name . ' не может быть пустым');
            }
        }

    }
}
