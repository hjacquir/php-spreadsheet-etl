<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 16:39
 */

namespace Hj\Config;

use Hj\Yaml\Child\Charset;
use Hj\Yaml\Child\DbName;
use Hj\Yaml\Child\Driver;
use Hj\Yaml\Child\Host;
use Hj\Yaml\Child\Password;
use Hj\Yaml\Child\Port;
use Hj\Yaml\Child\User;

/**
 * Class DatabaseConfig
 * @package Hj\Config
 */
class DatabaseConfig implements Config
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Host
     */
    private $host;

    /**
     * @var Charset
     */
    private $charset;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Password
     */
    private $password;

    /**
     * @var DbName
     */
    private $dbName;

    /**
     * @var Port
     */
    private $port;

    /**
     * DatabaseConfig constructor.
     * @param Driver $driver
     * @param Host $host
     * @param Charset $charset
     * @param User $user
     * @param Password $password
     * @param DbName $dbName
     * @param Port $port
     */
    public function __construct(
        Driver $driver,
        Host $host,
        Charset $charset,
        User $user,
        Password $password,
        DbName $dbName,
        Port $port
    )
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->charset = $charset;
        $this->user = $user;
        $this->password = $password;
        $this->dbName = $dbName;
        $this->port = $port;
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * @return Host
     */
    public function getHost(): Host
    {
        return $this->host;
    }

    /**
     * @return Charset
     */
    public function getCharset(): Charset
    {
        return $this->charset;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @return DbName
     */
    public function getDbName(): DbName
    {
        return $this->dbName;
    }

    /**
     * @return Port
     */
    public function getPort(): Port
    {
        return $this->port;
    }
}