<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 11:15
 */

namespace Hj\Validator\Data;

use Hj\Collector\ErrorCollector;
use Hj\Error\Data\AbstractDataError;
use Hj\File\CellAdapter;
use Hj\Normalizer\RemoveSpaceNormalizer;

/**
 * Class DataLengthReachedValidator
 * @package Hj\Validator\Data
 */
class DataLengthReachedValidator extends AbstractDataValidator
{
    /**
     * @var int
     */
    private $length;

    /**
     * @var RemoveSpaceNormalizer
     */
    private $removeSpaceNormalizer;

    /**
     * DataLengthReachedValidator constructor.
     * @param $length
     * @param ErrorCollector $errorCollector
     * @param AbstractDataError $associatedError
     * @param RemoveSpaceNormalizer $removeSpaceNormalizer
     */
    public function __construct(
        $length,
        ErrorCollector $errorCollector,
        AbstractDataError $associatedError,
        RemoveSpaceNormalizer $removeSpaceNormalizer
    )
    {
        parent::__construct(
            $errorCollector,
            $associatedError
        );
        $this->length = $length;
        $this->removeSpaceNormalizer = $removeSpaceNormalizer;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        $currentValue = $cellAdapter->getNormalizedValue();
        $removedSpaceValue = $this->removeSpaceNormalizer->normalize($currentValue);

        if (strlen($removedSpaceValue) <= $this->length) {
            return true;
        }

        return false;
    }
}