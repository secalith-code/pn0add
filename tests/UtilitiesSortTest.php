<?php

namespace App\PhpTests;

use ArgumentCountError;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use App\Utilities;

final class UtilitiesSortTest extends TestCase
{

    public function testSortTitleAscByDefault()
    {
        $input = $this->provideSortData();

        $expectedResult = [
            ['id' => 4, 'title' => 'Alpha'],
            ['title'=> 'Beta', 'id' => 98],
            ['id' => 1,'title' => 'Zeta'],
        ];

        self::assertEqualsCanonicalizing(
            $expectedResult,
            Utilities::sortArrayByKey($input,'title')
        );
    }

    public function testSortTitleAsc()
    {
        $input = $this->provideSortData();

        $expectedResult = [
            ['id' => 4, 'title' => 'Alpha'],
            ['title'=> 'Beta', 'id' => 98],
            ['id' => 1,'title' => 'Zeta'],
        ];

        self::assertEqualsCanonicalizing($expectedResult, Utilities::sortArrayByKey($input,'title', SORT_ASC));
    }

    public function testSortTitleDesc()
    {
        $input = $this->provideSortData();

        $expectedResult = [
            ['id' => 1,'title' => 'Zeta'],
            ['title'=> 'Beta', 'id' => 98],
            ['id' => 4, 'title' => 'Alpha'],
        ];

        self::assertEqualsCanonicalizing($expectedResult, Utilities::sortArrayByKey($input,'title', SORT_DESC));
    }

    public function provideSortData()
    {
        return [
            [
                'id' => 4,
                'title' => 'Alpha',
            ],
            [
                'id' => 1,
                'title' => 'Zeta',
            ],
            [
                'title'=> 'Beta',
                'id' => 98
            ],
        ];
    }
}