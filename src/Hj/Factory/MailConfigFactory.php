<?php
/**
 * Created by PhpStorm.
 * User: h.jacquir
 * Date: 24/07/2020
 * Time: 12:28
 */

namespace Hj\Factory;

use Hj\Config\MailsConfig;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Admins;
use Hj\Yaml\Child\From;
use Hj\Yaml\Child\Users;
use Hj\Yaml\Root\Mails;

/**
 * Class MailConfigFactory
 * @package Hj\Factory
 */
class MailConfigFactory implements ConfigFactory
{
    /**
     * @param string $yamlConfigFilePath
     * @return MailsConfig
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function createConfig($yamlConfigFilePath)
    {
        $yamlValidationIsStringObserver = new YamlValueIsStringValidationObserver(
            new ValueIsString()
        );
        $yamlValidationIsArrayObserver = new YamlValueIsArrayValidationObserver(
            new ValueIsArray()
        );

        $mailsConfigRoot = new Mails($yamlConfigFilePath, $yamlValidationIsArrayObserver);
        $from = new From($mailsConfigRoot, $yamlValidationIsStringObserver);
        $users = new Users($mailsConfigRoot, $yamlValidationIsArrayObserver);
        $admins = new Admins($mailsConfigRoot, $yamlValidationIsArrayObserver);
        return new MailsConfig(
            $from,
            $users,
            $admins
        );
    }

}