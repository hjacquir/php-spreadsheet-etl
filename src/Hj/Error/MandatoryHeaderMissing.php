<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 18:05
 */

namespace Hj\Error;

use Hj\Exception\AttributeNotSetException;

/**
 * Class MandatoryHeaderMissing
 * @package Hj\Error
 */
class MandatoryHeaderMissing implements Error
{
    /**
     * @var array
     */
    private $notFoundMandatoryHeaders = [];

    /**
     * @return string
     */
    public function getLevel()
    {
        return Error::CRITICAL;
    }

    /**
     * @return mixed|string
     * @throws AttributeNotSetException
     */
    public function getMessage()
    {
        if ($this->notFoundMandatoryHeaders === []) {
            throw new AttributeNotSetException("Please call the method setNotFoundMandatoryHeaders() before to call this method");
        }

        $message = "Spreadsheet-etl encountered an error." .
            " All mandatories headers are not present." .
        " The following headers are missing : " . implode(" , ", $this->notFoundMandatoryHeaders);

        return $message;
    }

    /**
     * @param array $notFoundMandatoryHeaders
     */
    public function setNotFoundMandatoryHeaders($notFoundMandatoryHeaders)
    {
        $this->notFoundMandatoryHeaders = $notFoundMandatoryHeaders;
    }

    /**
     * @return string
     */
    public function target()
    {
        return Error::TARGET_USER;
    }
}