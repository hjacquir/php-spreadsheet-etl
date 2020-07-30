<?php
/**
 * User: h.jacquir
 * Date: 22/01/2020
 * Time: 10:57
 */

namespace Hj\Error;

/**
 * Class FtpFailureDownloadFile
 * @package Hj\Error
 */
class FtpFailureDownloadFile implements Error
{
    /**
     * @var string
     */
    private $dirName = "";


    /**
     * @return string
     */
    public function getLevel()
    {
        return self::CRITICAL;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return "Spreadsheet-etl tried to download the files from the remote ftp : {$this->dirName} directory and encountered an error.";
    }

    /**
     * @return string
     */
    public function target()
    {
        return self::TARGET_ADMIN;
    }

    /**
     * @param string $dirName
     */
    public function setDirName($dirName)
    {
        $this->dirName = $dirName;
    }
}