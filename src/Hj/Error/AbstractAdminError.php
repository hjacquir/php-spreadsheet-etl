<?php
/**
 * User: h.jacquir
 * Date: 06/05/2020
 * Time: 11:30
 */

namespace Hj\Error;

/**
 * Class AbstractAdminError
 * @package Hj\Error
 */
abstract class AbstractAdminError implements Error
{
    const DEFAULT_VALUE_ERROR_MESSAGE = "default_value_error_message";

    /**
     * @var string
     */
    private $errorMessage = self::DEFAULT_VALUE_ERROR_MESSAGE;

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return Error::CRITICAL;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getErrorMessage();
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_ADMIN;
    }
}