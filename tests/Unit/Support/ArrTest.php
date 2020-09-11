<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Support;

use ElasticScoutDriverPlus\Support\Arr;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Support\Arr
 */
final class ArrTest extends TestCase
{
    public function test_normal_array_can_not_be_wrapped(): void
    {
        $array = ['foo', 'bar'];

        $this->assertSame($array, Arr::wrapAssoc($array));
    }

    public function test_assoc_array_can_be_wrapped(): void
    {
        $array = [
            'foo' => 1,
            'bar' => 2,
        ];

        $this->assertSame(
            [
                ['foo' => 1],
                ['bar' => 2],
            ],
            Arr::wrapAssoc($array)
        );
    }
}
