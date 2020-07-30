<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 11:12
 */

namespace Hj\Error\Data;

/**
 * Class DataLengthReachedError
 * @package Hj\Error\Data
 */
class DataLengthReachedError extends AbstractDataError
{
    /**
     * @var int
     */
    private $maximalLength;

    /**
     * DataLengthReachedError constructor.
     * @param int $maximalLength
     */
    public function __construct($maximalLength)
    {
        $this->maximalLength = $maximalLength;
    }

    /**
     * @return string
     */
    protected function getContextualMessage()
    {
        $message = "Spreadsheet-etl encountered an error." .
            " Data validation failed. The maximum number of characters allowed has been exceeded.\n";

        foreach ($this->getCellAdapterWithErrors() as $cellAdapterWithError) {
            $currentLength = strlen($cellAdapterWithError->getCell()->getValue());
            $message .= "Header : {$cellAdapterWithError->getAssociatedHeader()} (Column : {$cellAdapterWithError->getColumnName()})";
            $message .= " - Row : {$cellAdapterWithError->getRowIndex()} \n Number of characters allowed : {$this->maximalLength} Current number of characters {$currentLength}";
        }

        return $message;
    }
}