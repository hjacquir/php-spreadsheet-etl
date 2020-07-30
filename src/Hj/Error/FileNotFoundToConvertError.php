<?php
/**
 * User: h.jacquir
 * Date: 11/04/2020
 * Time: 11:09
 */

namespace Hj\Error;

/**
 * Class FileNotFoundToConvertError
 * @package Hj\Error
 */
class FileNotFoundToConvertError implements Error
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
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
        return "Spreadsheet-etl encountered an error while converting the file : {$this->getFileName()} to UTF-8." .
            " File conversion failed because file could not be found \n\n";
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_ADMIN;
    }
}