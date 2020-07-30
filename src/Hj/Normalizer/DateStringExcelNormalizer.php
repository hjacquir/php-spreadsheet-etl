<?php
/**
 * User: h.jacquir
 * Date: 15/04/2020
 * Time: 10:15
 */

namespace Hj\Normalizer;


use Hj\File\CellAdapter;
use Hj\File\Field\BirthDate;
use Hj\File\Field\Field;

/**
 * Class DateStringExcelNormalizer
 * @package Hj\Normalizer
 */
class DateStringExcelNormalizer implements Normalizer
{
    /**
     * @var Field
     */
    private $fieldDate;

    /**
     * DateStringExcelNormalizer constructor.
     * @param Field $fieldDate
     */
    public function __construct(Field $fieldDate)
    {
        $this->fieldDate = $fieldDate;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @return string
     */
    public function normalize($cellAdapter)
    {
        $currentValue = $cellAdapter->getCell()->getValue();

        if ($cellAdapter->getAssociatedHeader() === $this->fieldDate->getExpectedHeaderValue()) {
            $convertedValue = $this->fromExcelToLinux($currentValue);

            if (false !== $convertedValue) {
                return $convertedValue;
            }

            return $currentValue;
        }

        return $currentValue;
    }

    /**
     * @param string $excelTime
     *
     * @return false|string
     */
    private function fromExcelToLinux($excelTime) {
        if (is_numeric($excelTime)) {
            $time = ($excelTime - 25569) * 86400;

            return date(BirthDate::DATE_DATABASE_FORMAT, $time);
        }

        return $excelTime;
    }
}