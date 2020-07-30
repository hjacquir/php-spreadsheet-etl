<?php
/**
 * User: h.jacquir
 * Date: 20/03/2020
 * Time: 15:31
 */

namespace Hj\Validator\Data;

use Hj\Collector\ErrorCollector;
use Hj\Error\Data\AbstractDataError;
use Hj\File\CellAdapter;

/**
 * Class AuthorizedValueValidator
 * @package Hj\Validator\Data
 */
class AuthorizedValueValidator extends AbstractDataValidator
{
    /**
     * @var array
     */
    private $authorizedValues = [];

    /**
     * AuthorizedValueValidator constructor.
     * @param array $authorizedValues
     * @param ErrorCollector $errorCollector
     * @param AbstractDataError $associatedError
     */
    public function __construct(
        array $authorizedValues,
        ErrorCollector $errorCollector,
        AbstractDataError $associatedError
    )
    {
        parent::__construct(
            $errorCollector,
            $associatedError
        );
        $this->authorizedValues = $authorizedValues;
    }


    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        return in_array($cellAdapter->getNormalizedValue(), $this->authorizedValues);
    }
}