<?php
/**
 * User: h.jacquir
 * Date: 04/02/2020
 * Time: 09:14
 */

namespace Hj\tests\Unit\Normalizer;

use Hj\Normalizer\FloatNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class FloatNormalizerTest
 *
 * @package Hj\tests\Unit\Normalizer
 * @covers \Hj\Normalizer\FloatNormalizer
 */
class FloatNormalizerTest extends TestCase
{
    /**
     * @var FloatNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new FloatNormalizer();
    }

    /**
     * @param $expected
     * @param $currentValue
     * @dataProvider dataProvider
     */
    public function testNormalize($expected, $currentValue)
    {
        self::assertSame($expected, $this->normalizer->normalize($currentValue));
    }

    public function dataProvider()
    {
        return [
            [
                1325125.54,
                '1.325.125,54'
            ],
            [
                1325125.54,
                '1,325,125.54'
            ],
            [
                59.95,
                '59,95'
            ],
            [
                12000.30,
                '12.000,30'
            ],
            [
                12000.30,
                '12,000.30'
            ]
        ];
    }
}