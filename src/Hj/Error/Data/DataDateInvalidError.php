<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 13:29
 */

namespace Hj\Error\Data;

/**
 * Class DataDateInvalidError
 * @package Hj\Error\Data
 */
class DataDateInvalidError extends AbstractDataError
{
    /**
     * @return string
     */
    protected function getContextualMessage()
    {
        $message = "Spreadsheet-etl encountered an error. Data validation failed." .
        " The dates below are invalid or do not respect the expected format.".
        " \n";

        foreach ($this->getCellAdapterWithErrors() as $cellAdapterWithError) {
            $message .= "Header : {$cellAdapterWithError->getAssociatedHeader()} (Column : {$cellAdapterWithError->getColumnName()})";
            $message .= " - Row : {$cellAdapterWithError->getRowIndex()} Current value : {$cellAdapterWithError->getCell()->getValue() }\n";
        }

        return $message;
    }
}