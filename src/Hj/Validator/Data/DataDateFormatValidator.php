<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 13:19
 */

namespace Hj\Validator\Data;

use DateTime;
use Hj\Collector\ErrorCollector;
use Hj\Error\Data\AbstractDataError;
use Hj\File\CellAdapter;
use Hj\Normalizer\RemoveSpaceNormalizer;
use Hj\ValidationByPasser;

/**
 * Class DataDateFormatValidator
 * @package Hj\Validator\Data
 */
class DataDateFormatValidator extends AbstractDataValidator
{
    const ACCEPTED_FORMATS = [
        'd/m/Y',
        'd-m-Y',
    ];

    /**
     * @var string
     */
    private $currentFormat = '';

    /**
     * @var ValidationByPasser
     */
    private $validationByPasser;

    /**
     * @var RemoveSpaceNormalizer
     */
    private $removeSpaceNormalizer;

    /**
     * DataDateFormatValidator constructor.
     * @param ErrorCollector $errorCollector
     * @param AbstractDataError $associatedError
     * @param RemoveSpaceNormalizer $removeSpaceNormalizer
     * @param ValidationByPasser $validationByPasser
     */
    public function __construct(
        ErrorCollector $errorCollector,
        AbstractDataError $associatedError,
        RemoveSpaceNormalizer $removeSpaceNormalizer,
        ValidationByPasser $validationByPasser
    )
    {
        parent::__construct(
            $errorCollector,
            $associatedError
        );
        $this->removeSpaceNormalizer = $removeSpaceNormalizer;
        $this->validationByPasser = $validationByPasser;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        $currentValue = $cellAdapter->getNormalizedValue();
        $spaceRemovedValue = $this->removeSpaceNormalizer->normalize($currentValue);

        if ($this->validationByPasser->bypassValidation($spaceRemovedValue)) {
            return true;
        }


        foreach (self::ACCEPTED_FORMATS as $format) {
            if (true === $this->validateDate($spaceRemovedValue, $format)) {
                $this->currentFormat = $format;

                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentFormat(): string
    {
        return $this->currentFormat;
    }

    /**
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function validateDate($date, $format)
    {
        DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();

        return $errors['warning_count'] === 0 && $errors['error_count'] === 0;
    }
}