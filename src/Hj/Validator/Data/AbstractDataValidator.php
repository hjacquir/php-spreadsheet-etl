<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 08:36
 */

namespace Hj\Validator\Data;

use Hj\Collector\ErrorCollector;
use Hj\Error\Data\AbstractDataError;
use Hj\File\CellAdapter;
use Hj\Validator\Validator;

/**
 * Class AbstractDataValidator
 * @package Hj\Validator\Data
 */
abstract class AbstractDataValidator implements Validator
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var CellAdapter[]
     */
    private $cellWithErrors = [];

    /**
     * @var AbstractDataError
     */
    private $associatedError;

    /**
     * AbstractDataValidator constructor.
     * @param ErrorCollector $errorCollector
     * @param AbstractDataError $associatedError
     */
    public function __construct(
        ErrorCollector $errorCollector,
        AbstractDataError $associatedError
    )
    {
        $this->errorCollector = $errorCollector;
        $this->associatedError = $associatedError;
    }

    /**
     * @param CellAdapter $value
     */
    public function valid($value)
    {
        if (false === $this->isValid($value)) {
            $this->addCellAdapterWithError($value);
        }
    }

    /**
     * @return ErrorCollector
     */
    public function getErrorCollector()
    {
        return $this->errorCollector;
    }

    /**
     * @return CellAdapter[]
     */
    public function getCellWithErrors()
    {
        return $this->cellWithErrors;
    }

    /**
     * @param CellAdapter $cellAdapter
     */
    public function addCellAdapterWithError(CellAdapter $cellAdapter)
    {
        array_push($this->cellWithErrors, $cellAdapter);
    }

    /**
     * @return bool
     */
    private function hasErrors()
    {
        return count($this->cellWithErrors) > 0;
    }

    public function logError()
    {
        if ($this->hasErrors()) {
            $this->associatedError->setCellAdapterWithErrors($this->getCellWithErrors());
            $this->getErrorCollector()->addError($this->associatedError);
        }
    }

    /**
     * @return AbstractDataError
     */
    public function getAssociatedError(): AbstractDataError
    {
        return $this->associatedError;
    }

    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     */
    public abstract function isValid(CellAdapter $cellAdapter);
}