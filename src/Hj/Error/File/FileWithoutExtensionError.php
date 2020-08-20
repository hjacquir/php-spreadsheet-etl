<?php
/**
 * User: h.jacquir
 * Date: 20/08/2020
 * Time: 16:00
 */

namespace Hj\Error\File;

use Hj\Error\Error;

/**
 * Class FileWithoutExtensionError
 * @package Hj\Error\File
 */
class FileWithoutExtensionError implements Error
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
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
        return "The file : '{$this->filePath}' does not have an extension. Please check your file.";
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_ADMIN;
    }
}