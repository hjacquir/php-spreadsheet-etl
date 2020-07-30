<?php
/**
 * User: h.jacquir
 * Date: 04/02/2020
 * Time: 09:14
 */

namespace Hj\tests\Unit\Normalizer;

use Hj\Normalizer\TrimNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class TrimNormalizerTest
 * @package Hj\tests\Unit\Normalizer
 * @covers \Hj\Normalizer\TrimNormalizer
 */
class TrimNormalizerTest extends TestCase
{
    /**
     * @var TrimNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new TrimNormalizer();
    }

    /**
     * @param $expected
     * @param $currentValue
     * @dataProvider dataProvider
     */
    public function testNormalizeRemoveChar($expected, $currentValue)
    {
        self::assertSame($expected, $this->normalizer->normalize($currentValue));
    }

    public function dataProvider()
    {
        return [
            // tabulation
            [
                "bla",
                "   bla "
            ],
            // espace
            [
                "bla",
                "  bla  "
            ],
            // nouvelle ligne
            [
                "bla",
                "  
                bla
                  
                  "
            ],
            // retour chariot
            [
                "bla",
                "bla
                "
            ],
            // double quote
            [
                'bla',
                '"bla"'
            ],
            // simple quote
            [
                'bla',
                '\'bla\'\''
            ],
            // ;
            [
                'bla;bla',
                ';;;bla;bla ;;;;'
            ],
            // ,
            [
                'bla',
                ',,,, bla,,'
            ],
            // .
            [
                'bla. foo',
                '...bla. foo ..'
            ],
            // +
            [
                'bla + foo',
                '+bla + foo +'
            ],
            // -
            [
                'bla - foo',
                '--- bla - foo -'
            ],
            // _
            [
                'bla_foo',
                '____bla_foo ____'
            ],
            // ?
            [
                'bla_foo',
                '???bla_foo? '
            ],
            // !
            [
                'bla_foo',
                '!!bla_foo! '
            ],
            // &
            [
                'bla&foo',
                '&&&bla&foo& '
            ],
            // :
            [
                'blafoo',
                '::: blafoo : '
            ],
            // /
            [
                'bla/foo',
                '//bla/foo/ '
            ],
        ];
    }
}