<?php
/**
 * User: h.jacquir
 * Date: 26/02/2020
 * Time: 13:54
 */

namespace Hj\Error;

/**
 * Class ConfigFileMismatchError
 * @package Hj\Error
 */
class ConfigFileMismatchError implements Error
{
    /**
     * @var array
     */
    private $mismatchedKeys = [];

    /**
     * @return string
     */
    public function getLevel()
    {
        return Error::CRITICAL;
    }

    /**
     * @return array
     */
    public function getMismatchedKeys(): array
    {
        return $this->mismatchedKeys;
    }

    /**
     * @param array $mismatchedKeys
     */
    public function setMismatchedKeys(array $mismatchedKeys): void
    {
        $this->mismatchedKeys = $mismatchedKeys;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        $message = "Spreadsheet-etl had encountered an error. " .
            "The headers below does not have extraction fields defined. " .
            "Please check the correspondence between the configuration file and the extraction fields.\n\n";
        $message .= implode(", ", $this->mismatchedKeys);

        return $message;
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_ADMIN;
    }
}