<?php


declare(strict_types=1);

namespace App\Service;

use App\Dto\PersonalDataDto;

final class CoupleService
{
    const int ACCEPTABLE_DIFFERENCE = 10;

    /**
     * Check couple compatibility
     *
     * @param PersonalDataDto $wife
     * @param PersonalDataDto $husband
     * @return bool
     */
    public function checkPairCompatibility(PersonalDataDto $wife, PersonalDataDto $husband): bool
    {
        return
            abs(mb_strlen($husband->name) - mb_strlen($wife->name)) <= static::ACCEPTABLE_DIFFERENCE
            && abs($husband->age - $wife->age) <= static::ACCEPTABLE_DIFFERENCE;
    }

    /**
     * Finds compatible pairs from given arrays of available husbands and wives.
     *
     * @param array{
     *     free_husband: array<array-key, array{name: string, age: int}>,
     *     free_wife: array<array-key, array{name: string, age: int}>
     * } $people Associative array containing:
     *             - free_husband: List of available husbands
     *             - free_wife: List of available wives
     *
     * @return array<array{
     *     wife: PersonalDataDto,
     *     husband: PersonalDataDto
     * }> Returns an array of compatible pairs where each pair contains DTO objects
     */
    public function findPotentialCouple(array $people): array
    {
        if (empty($people)) {
            return [];
        }

        $pairs = [];

        foreach ($people['free_wife'] as $wifeData) {
            foreach ($people['free_husband'] as $husbandData) {
                $wife = new PersonalDataDto($wifeData['name'], $wifeData['age']);
                $husband = new PersonalDataDto($husbandData['name'], $husbandData['age']);

                if ($this->checkPairCompatibility($wife, $husband)) {
                    $pairs[] = [
                        'wife' => $wife,
                        'husband' => $husband
                    ];
                }
            }
        }

        return $pairs;
    }
}
