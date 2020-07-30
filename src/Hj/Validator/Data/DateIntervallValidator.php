<?php
/**
 * User: h.jacquir
 * Date: 08/06/2020
 * Time: 14:58
 */

namespace Hj\Validator\Data;

use DateTime;
use Hj\Collector\ErrorCollector;
use Hj\Error\Data\AbstractDataError;
use Hj\Error\Data\DataDateIntervalInvalidError;
use Hj\File\CellAdapter;
use Hj\Normalizer\RemoveSpaceNormalizer;

/**
 * Class DateIntervallValidator
 * @package Hj\Validator\Data
 */
class DateIntervallValidator extends AbstractDataValidator
{
    /**
     * @var RemoveSpaceNormalizer
     */
    private $removeSpaceNormalizer;

    /**
     * @var DataDateFormatValidator
     */
    private $dataDateFormatValidator;

    /**
     * @var int
     */
    private $isInTestContext = 0;

    /**
     * DataDateFormatValidator constructor.
     * @param int $isInTestContext
     * @param ErrorCollector $errorCollector
     * @param AbstractDataError $associatedError
     * @param RemoveSpaceNormalizer $removeSpaceNormalizer
     * @param DataDateFormatValidator $dataDateFormatValidator
     */
    public function __construct(
        $isInTestContext,
        ErrorCollector $errorCollector,
        AbstractDataError $associatedError,
        RemoveSpaceNormalizer $removeSpaceNormalizer,
        DataDateFormatValidator $dataDateFormatValidator
    ) {
        parent::__construct(
            $errorCollector,
            $associatedError
        );
        $this->removeSpaceNormalizer = $removeSpaceNormalizer;
        $this->dataDateFormatValidator = $dataDateFormatValidator;
        $this->isInTestContext = $isInTestContext;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     * @throws \Exception
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        // in test context we bypass the validation
        if (1 === $this->isInTestContext) {
            return true;
        }

        // validate intervall only if format is valid
        if ($this->dataDateFormatValidator->isValid($cellAdapter)) {
            return $this->validateIntervall($cellAdapter, $this->dataDateFormatValidator->getCurrentFormat());
        }

        return true;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @param string $format
     * @return bool
     * @throws \Exception
     */
    private function validateIntervall(CellAdapter $cellAdapter, $format)
    {
        $isValid = true;

        $currentValue = $cellAdapter->getNormalizedValue();

        $spaceRemovedValue = $this->removeSpaceNormalizer
            ->normalize($currentValue);

        $currentDateAsDatetime = new DateTime();
        $currentDateAsString = $currentDateAsDatetime->format("d/m/Y");

        /** @var DataDateIntervalInvalidError $associatedError */
        $associatedError = $this->getAssociatedError();
        $associatedError->setCurrentDateAsString($currentDateAsString);

        $currentValueAsDatetime = DateTime::createFromFormat($format, $spaceRemovedValue);

        // if current value is after current time = invalid
        if ($currentDateAsDatetime < $currentValueAsDatetime) {
            $isValid = false;
        }

        $currentDateYearAsString = $currentDateAsDatetime->format('Y');
        $previousYearAsInt = (int)$currentDateYearAsString - 1;
        $previousDateAsString = "01/01/{$previousYearAsInt}";

        $associatedError->setPreviousDateAsString($previousDateAsString);
        $previousDatAsDateTime = new DateTime($previousDateAsString);

        // if current value is before previous year = invalid
        if ($isValid === true && $currentValueAsDatetime < $previousDatAsDateTime) {
            $isValid = false;
        }

        return $isValid;
    }
}