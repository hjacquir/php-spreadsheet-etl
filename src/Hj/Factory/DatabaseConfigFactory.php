<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:20
 */

namespace Hj\Factory;

use Hj\Config\DatabaseConfig;
use Hj\Exception\KeyNotExist;
use Hj\Exception\WrongTypeException;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Url;
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
     * @throws KeyNotExist
     * @throws WrongTypeException
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
        $url = new Url($database, $yamlValidationIsStringObserver);

        return new DatabaseConfig(
            $url
        );
    }
}