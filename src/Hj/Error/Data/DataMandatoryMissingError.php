<?php
/**
 * User: h.jacquir
 * Date: 19/02/2020
 * Time: 15:34
 */

namespace Hj\Error\Data;

/**
 * Class DataMandatoryMissingError
 * @package Hj\Error\Data
 */
class DataMandatoryMissingError extends AbstractDataError
{
    /**
     * @return string
     */
    protected function getContextualMessage()
    {
        $message = "Data validation failed. The mandatory data below is missing.\n";

        foreach ($this->getCellAdapterWithErrors() as $cellAdapterWithError) {
            $message .= "Header : {$cellAdapterWithError->getAssociatedHeader()} (Column : {$cellAdapterWithError->getColumnName()})";
            $message .= " - Row : {$cellAdapterWithError->getRowIndex()} \n";
        }

        return $message;
    }
}