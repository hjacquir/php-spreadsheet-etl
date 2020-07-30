<?php
/**
 * Created by PhpStorm.
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 13:33
 */

namespace Hj\Tests\Unit\Validator\Data;

use Hj\Collector\ErrorCollector;
use Hj\Error\Data\DataDateInvalidError;
use Hj\File\CellAdapter;
use Hj\Helper\Tests\AbstractTestCase;
use Hj\Normalizer\RemoveSpaceNormalizer;
use Hj\ValidationByPasser;
use Hj\Validator\Data\DataDateFormatValidator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class DataDateFormatValidatorTest
 * @package Hj\Tests\Unit\Validator\Data
 * @covers \Hj\Validator\Data\DataDateFormatValidator
 */
class DataDateFormatValidatorTest extends AbstractTestCase
{
    /**
     * @var DataDateFormatValidator
     */
    private $validator;

    /**
     * @var ErrorCollector|MockObject
     */
    private $errorCollector;

    /**
     * @var DataDateInvalidError|MockObject
     */
    private $associatedError;

    /**
     * @var RemoveSpaceNormalizer|MockObject
     */
    private $removeSpaceNormalizer;

    /**
     * @var ValidationByPasser|MockObject
     */
    private $validationByPasser;

    /**
     * @var CellAdapter|MockObject
     */
    private $cellAdapter;

    public function setUp()
    {
        $this->errorCollector = $this->getMockConstructorDisabled(ErrorCollector::class);
        $this->associatedError = $this->getMockConstructorDisabled(DataDateInvalidError::class);
        $this->removeSpaceNormalizer = $this->getMockConstructorDisabled(RemoveSpaceNormalizer::class);
        $this->validationByPasser = $this->getMockConstructorDisabled(ValidationByPasser::class);
        $this->cellAdapter = $this->getMockConstructorDisabled(CellAdapter::class);

        $this->validator = new DataDateFormatValidator(
            $this->errorCollector,
            $this->associatedError,
            $this->removeSpaceNormalizer,
            $this->validationByPasser
        );
    }

    /**
     * @param bool $expectedResult
     * @param string $date
     *
     * @dataProvider provideDataIsValid
     */
    public function testIsValid($expectedResult, $date)
    {
        $this->cellAdapter
            ->method('getNormalizedValue')
            ->willReturn($date);
        $this->removeSpaceNormalizer->method('normalize')->with($date)->willReturn($date);
        self::assertSame($expectedResult, $this->validator->isValid($this->cellAdapter));
    }

    public function testIsValidReturnTrueWhenByPassedNullValueIsGiven()
    {
        $this->cellAdapter
            ->method('getNormalizedValue')
            ->willReturn(null);
        $this->validationByPasser
            ->method("bypassValidation")
            ->with(null)
            ->willReturn(true);
        $this->validator = new DataDateFormatValidator(
            $this->errorCollector,
            $this->associatedError,
            $this->removeSpaceNormalizer,
            $this->validationByPasser
        );
        self::assertSame(true, $this->validator->isValid($this->cellAdapter));
    }

    public function testIsValidReturnTrueWhenByPassedEmptyStringValueIsGiven()
    {
        $this->cellAdapter
            ->method('getNormalizedValue')
            ->willReturn("");

        $this->validationByPasser
            ->method("bypassValidation")
            ->with("")
            ->willReturn(true);
        $this->validator = new DataDateFormatValidator(
            $this->errorCollector,
            $this->associatedError,
            $this->removeSpaceNormalizer,
            $this->validationByPasser
        );
        self::assertSame(true, $this->validator->isValid($this->cellAdapter));
    }

    /**
     * @return array
     */
    public function provideDataIsValid()
    {
        return [
            [
                false,
                'foo',
            ],
            [
                false,
                '1970-01-01',
            ],
            [
                false,
                '1970/01/01',
            ],
            [
                true,
                '01/01/1970',
            ],
            [
                true,
                '01-01-1970',
            ],
            [
                true,
                '01-01-70',
            ],
            [
                true,
                '01/01/70',
            ],
            [
                false,
                '30/02/70',
            ],
            [
                false,
                '30/02/1970',
            ],
        ];
    }
}