<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 15:33
 */

namespace Hj\Tests\Unit\Normalizer;

use Hj\Normalizer\AccentsRemoverNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class AccentsRemoverNormalizerTest
 * @package Hj\Tests\Unit\Normalizer
 * @covers \Hj\Normalizer\AccentsRemoverNormalizer
 */
class AccentsRemoverNormalizerTest extends TestCase
{
    public function testNormalizeRemoveAccents()
    {
        $normalizer = new AccentsRemoverNormalizer();
        self::assertSame("a^zerte", $normalizer->normalize("à^zërté"));
    }
}