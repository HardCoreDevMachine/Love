<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\ValidatorHelper;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class ValidatorHelperTest extends TestCase
{
    /** @var ValidatorHelper */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new ValidatorHelper([
            'name' => 'string',
            'age' => 'integer',
            'is_active' => 'boolean',
            'price' => 'double',
        ]);
    }

    public function testValidDataPassesValidation(): void
    {
        $validData = [
            'name' => 'John Doe',
            'age' => 30,
            'is_active' => true,
            'price' => 19.99,
        ];

        $this->validator->arrayValidate($validData);

        // Если не выброшено исключение, тест пройден
        $this->assertTrue(true);
    }

    public function testMissingFieldThrowsException(): void
    {
        $invalidData = [
            'name' => 'John Doe',
            // Пропущено поле 'age'
            'is_active' => true,
            'price' => 19.99,
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле age отсутствует');

        $this->validator->arrayValidate($invalidData);
    }

    public function testWrongTypeThrowsException(): void
    {
        $invalidData = [
            'name' => 'John Doe',
            'age' => '30', // Должно быть integer
            'is_active' => true,
            'price' => 19.99,
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле age должно быть типа integer');

        $this->validator->arrayValidate($invalidData);
    }

    public function testEmptyStringThrowsException(): void
    {
        $invalidData = [
            'name' => '', // Пустая строка
            'age' => 30,
            'is_active' => true,
            'price' => 19.99,
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле name не может быть пустым');

        $this->validator->arrayValidate($invalidData);
    }

    public function testZeroValuesAreValid(): void
    {
        $validData = [
            'name' => '0', // Строка '0' не считается пустой
            'age' => 0,    // 0 - валидное число
            'is_active' => false,
            'price' => 0.0,
        ];

        $this->validator->arrayValidate($validData);

        $this->assertTrue(true);
    }

    public function testNullValueThrowsException(): void
    {
        $invalidData = [
            'name' => 'John Doe',
            'age' => null, // null недопустим
            'is_active' => true,
            'price' => 19.99,
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле age должно быть типа integer');

        $this->validator->arrayValidate($invalidData);
    }

    public function testEmptyArrayThrowsException(): void
    {
        $invalidData = []; // Все обязательные поля отсутствуют

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле name отсутствует');

        $this->validator->arrayValidate($invalidData);
    }
}
