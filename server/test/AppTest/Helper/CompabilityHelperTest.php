<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Dto\Person;
use App\Helper\CompabilityHelper;
use PHPUnit\Framework\TestCase;

class CompabilityHelperTest extends TestCase
{
    public function testHappyPath(): void
    {
        $testMan = new Person('Anya', 19);
        $testWoman = new Person('Anton', 20);
        self::assertTrue(CompabilityHelper::compabilityCheck($testWoman, $testMan));
    }
}
