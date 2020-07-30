<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 08:31
 */

namespace Hj\Error\Data;

use Hj\Error\Error;
use Hj\File\CellAdapter;

/**
 * Class AbstractDataError
 * @package Hj\Error\Data
 */
abstract class AbstractDataError implements Error
{
    /**
     * @var array
     */
    private $cascadeFieldCurrentValues = [];

    /**
     * @var CellAdapter[]
     */
    private $cellAdapterWithErrors;

    /**
     * @param $key
     * @return mixed
     */
    public function getCascadeFieldCurrentValues($key)
    {
        return $this->cascadeFieldCurrentValues[$key];
    }

    /**
     * @param $cascadeFieldCurrentValue
     */
    public function addCascadeFieldCurrentValues($cascadeFieldCurrentValue): void
    {
        array_push($this->cascadeFieldCurrentValues, $cascadeFieldCurrentValue);
    }

    /**
     * @param CellAdapter[] $cellAdapterWithErrors
     */
    public function setCellAdapterWithErrors($cellAdapterWithErrors)
    {
        $this->cellAdapterWithErrors = $cellAdapterWithErrors;
    }

    /**
     * @return CellAdapter[]
     */
    public function getCellAdapterWithErrors()
    {
        return $this->cellAdapterWithErrors;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return Error::CRITICAL;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->getContextualMessage();
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_USER;
    }

    /**
     * @return string
     */
    protected abstract function getContextualMessage();
}