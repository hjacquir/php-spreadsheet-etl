<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 16:07
 */

namespace Hj\Config;

use Hj\Yaml\Child\Host;

/**
 * Class SmtpConfig
 * @package Hj\Config
 */
class SmtpConfig implements Config
{
    /**
     * @var Host
     */
    private $host;

    /**
     * SmtpConfig constructor.
     * @param Host $host
     */
    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    /**
     * @return Host
     */
    public function getHost(): Host
    {
        return $this->host;
    }
}