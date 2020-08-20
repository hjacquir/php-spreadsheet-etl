<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 16:09
 */

namespace Hj\Factory;

use Hj\Config\Config;
use Hj\Config\SmtpConfig;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Host;
use Hj\Yaml\Root\Smtp;

/**
 * Class SmtpConfigFactory
 * @package Hj\Factory
 */
class SmtpConfigFactory implements ConfigFactory
{
    /**
     * @param string $yamlConfigPath
     * @return SmtpConfig
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

        $smtp = new Smtp($yamlConfigPath, $yamlValidationIsArrayObserver);
        $host = new Host($smtp, $yamlValidationIsStringObserver);

        return new SmtpConfig(
            $host
        );
    }
}