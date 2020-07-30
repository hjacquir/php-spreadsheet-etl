<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 11:09
 */

namespace Hj\Error;

/**
 * Class HeaderNotOnFirstRowError
 * @package Hj\Error
 */
class HeaderNotOnFirstRowError implements Error
{
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
        return "Spreadsheet-etl had encountered an error. The header is not on the first row." .
            " Spreadsheet-etl only supports first line headers.";
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_USER;
    }
}