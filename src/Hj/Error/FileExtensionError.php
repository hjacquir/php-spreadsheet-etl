<?php
/**
 * User: h.jacquir
 * Date: 15/01/2020
 * Time: 10:44
 */

namespace Hj\Error;

/**
 * Class FileExtensionError
 * @package Hj\Error
 */
class FileExtensionError implements Error
{
    /**
     * @return string
     */
    public function getLevel(): string
    {
        return Error::CRITICAL;
    }

    /**
     * @param array $parameter
     * @return string
     */
    public function getMessage(): string
    {
        return "Spreadsheet-etl had encountered an error. File format is not supported.";
    }

    /**
     * @return string
     */
    public function target()
    {
        return self::TARGET_USER;
    }
}