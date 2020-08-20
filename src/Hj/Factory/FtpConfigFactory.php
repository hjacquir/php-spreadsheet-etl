<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 16:01
 */

namespace Hj\Factory;

use Hj\Config\Config;
use Hj\Config\FtpConfig;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Directory;
use Hj\Yaml\Child\Host;
use Hj\Yaml\Child\Password;
use Hj\Yaml\Child\Port;
use Hj\Yaml\Child\UserName;
use Hj\Yaml\Root\Ftp;

/**
 * Class FtpConfigFactory
 * @package Hj\Factory
 */
class FtpConfigFactory implements ConfigFactory
{
    /**
     * @param string $yamlConfigPath
     * @return FtpConfig
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function createConfig($yamlConfigPath)
    {
        $yamlValidationIsArrayObserver = new YamlValueIsArrayValidationObserver(
            new ValueIsArray()
        );
        $yamlValidationIsStringObserver = new YamlValueIsStringValidationObserver(
            new ValueIsString()
        );

        $ftp = new Ftp($yamlConfigPath, $yamlValidationIsArrayObserver);
        $host = new Host($ftp, $yamlValidationIsStringObserver);
        $userName = new UserName($ftp, $yamlValidationIsStringObserver);
        $password = new Password($ftp, $yamlValidationIsStringObserver);
        $directory = new Directory($ftp, $yamlValidationIsStringObserver);
        $port = new Port($ftp, $yamlValidationIsStringObserver);

        return new FtpConfig(
            $host,
            $userName,
            $password,
            $directory,
            $port
        );
    }
}