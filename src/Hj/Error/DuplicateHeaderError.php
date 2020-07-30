<?php
/**
 * User: h.jacquir
 * Date: 11/02/2020
 * Time: 09:23
 */

namespace Hj\Error;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * Class DuplicateHeaderError
 * @package Hj\Error
 */
class DuplicateHeaderError implements Error
{
    /**
     * @var Cell[]
     */
    private $duplicatedHeaders;

    /**
     * @param Cell[] $duplicatedHeaders
     */
    public function setDuplicatedHeaders($duplicatedHeaders)
    {
        $this->duplicatedHeaders = $duplicatedHeaders;
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
        $message = "Spreadsheet-etl had encountered an error." .
            " A header should appear only once in your file." .
            " The following headers are duplicated : ";

        $formattedMessage = [];

        foreach ($this->duplicatedHeaders as $columName => $duplicatedHeader) {
            array_push($formattedMessage, "{$duplicatedHeader->getValue()} (column : {$columName})");
        }

        $message .= implode(" , ", $formattedMessage);

        return $message;
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_USER;
    }
}