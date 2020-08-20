<?php
/**
 * Created by PhpStorm.
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:59
 */

namespace Hj\Config;

use Hj\Yaml\Child\Directory;
use Hj\Yaml\Child\Host;
use Hj\Yaml\Child\Password;
use Hj\Yaml\Child\Port;
use Hj\Yaml\Child\UserName;

/**
 * Class FtpConfig
 * @package Hj\Config
 */
class FtpConfig implements Config
{
    /**
     * @var Host
     */
    private $host;

    /**
     * @var UserName
     */
    private $userName;

    /**
     * @var Password
     */
    private $password;

    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var Port
     */
    private $port;

    /**
     * FtpConfig constructor.
     * @param Host $host
     * @param UserName $userName
     * @param Password $password
     * @param Directory $directory
     * @param Port $port
     */
    public function __construct(
        Host $host,
        UserName $userName,
        Password $password,
        Directory $directory,
        Port $port
    )
    {
        $this->host = $host;
        $this->userName = $userName;
        $this->password = $password;
        $this->directory = $directory;
        $this->port = $port;
    }

    /**
     * @return Host
     */
    public function getHost(): Host
    {
        return $this->host;
    }

    /**
     * @return UserName
     */
    public function getUserName(): UserName
    {
        return $this->userName;
    }

    /**
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * @return Port
     */
    public function getPort(): Port
    {
        return $this->port;
    }
}