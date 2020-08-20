<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:20
 */

namespace Hj\Factory;

use Hj\Config\DatabaseConfig;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Charset;
use Hj\Yaml\Child\DbName;
use Hj\Yaml\Child\Driver;
use Hj\Yaml\Child\Host;
use Hj\Yaml\Child\Password;
use Hj\Yaml\Child\Port;
use Hj\Yaml\Child\User;
use Hj\Yaml\Root\Database;

/**
 * Class DatabaseConfigFactory
 * @package Hj\Factory
 */
class DatabaseConfigFactory implements ConfigFactory
{
    /**
     * @param string $yamlConfigPath
     * @return array|DatabaseConfig
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function createConfig($yamlConfigPath)
    {
        $yamlValidationIsStringObserver = new YamlValueIsStringValidationObserver(
            new ValueIsString()
        );
        $yamlValidationIsArrayObserver = new YamlValueIsArrayValidationObserver(
            new ValueIsArray()
        );

        $database = new Database($yamlConfigPath, $yamlValidationIsArrayObserver);
        $driver = new Driver($database, $yamlValidationIsStringObserver);
        $hostConfig = new Host($database, $yamlValidationIsStringObserver);
        $charset = new Charset($database, $yamlValidationIsStringObserver);
        $user = new User($database, $yamlValidationIsStringObserver);
        $passWord = new Password($database, $yamlValidationIsStringObserver);
        $dbName = new DbName($database, $yamlValidationIsStringObserver);
        $port = new Port($database, $yamlValidationIsStringObserver);

        return new DatabaseConfig(
            $driver,
            $hostConfig,
            $charset,
            $user,
            $passWord,
            $dbName,
            $port
        );
    }
}