<?php
/**
 * User: h.jacquir
 * Date: 22/01/2020
 * Time: 10:47
 */

namespace Hj\Error;

/**
 * Class FtpFailureConnexion
 * @package Hj\Error
 */
class FtpFailureConnexion implements Error
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * FtpFailureConnexion constructor.
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return Error::CRITICAL;
    }

    /**
     * @param $parameter
     * @return mixed
     */
    public function getMessage()
    {
        return "Spreadsheet-etl encountered an error while connecting to the FTP server. Please check the server FTP settings : " . $this->exception->getMessage();
    }

    /**
     * @return string
     */
    public function target()
    {
        return self::TARGET_ADMIN;
    }
}