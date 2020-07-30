<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 09:32
 */

namespace Hj\Error;

/**
 * Class FileWithMultipleSheetsError
 * @package Hj\Error
 */
class FileWithMultipleSheetsError implements Error
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
        return "Spreadsheet-etl had encountered an error. The file has multiple sheets. Spreadsheet-etl only supports files with one sheet.";
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_USER;
    }
}