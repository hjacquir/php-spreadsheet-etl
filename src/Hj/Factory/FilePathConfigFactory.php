<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:52
 */

namespace Hj\Factory;

use Hj\Config\Config;
use Hj\Config\FilePathConfig;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Observer\YamlValueIsStringValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Validator\ValueIsString;
use Hj\Yaml\Child\Archived;
use Hj\Yaml\Child\Failure;
use Hj\Yaml\Child\InProcessing;
use Hj\Yaml\Child\Waiting;
use Hj\Yaml\Root\FilePath;

/**
 * Class FilePathConfigFactory
 * @package Hj\Factory
 */
class FilePathConfigFactory implements ConfigFactory
{
    /**
     * @param string $yamlConfigPath
     * @return Config|FilePathConfig
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

        $filePath = new FilePath($yamlConfigPath, $yamlValidationIsArrayObserver);
        $waitingDirConfig = new Waiting($filePath, $yamlValidationIsStringObserver);
        $inProcessingDirConfig = new InProcessing($filePath, $yamlValidationIsStringObserver);
        $archivedDirConfig = new Archived($filePath, $yamlValidationIsStringObserver);
        $failureDirConfig = new Failure($filePath, $yamlValidationIsStringObserver);

        return new FilePathConfig(
            $waitingDirConfig,
            $inProcessingDirConfig,
            $archivedDirConfig,
            $failureDirConfig
        );
    }
}