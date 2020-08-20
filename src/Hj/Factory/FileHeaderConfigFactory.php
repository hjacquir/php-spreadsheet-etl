<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:34
 */

namespace Hj\Factory;

use Hj\Config\Config;
use Hj\Config\FileHeadersConfig;
use Hj\Exception\YamlValueAreDuplicatedException;
use Hj\Observer\YamlValueIsArrayValidationObserver;
use Hj\Validator\ValueIsArray;
use Hj\Yaml\Child\CommonMandatoryHeaders;
use Hj\Yaml\Child\OptionalHeaders;
use Hj\Yaml\Root\File;

/**
 * Class FileHeaderConfigFactory
 * @package Hj\Factory
 */
class FileHeaderConfigFactory implements ConfigFactory
{
    /**
     * @var File
     */
    private $file;

    /**
     * @param string $yamlConfigPath
     * @return Config|FileHeadersConfig
     * @throws YamlValueAreDuplicatedException
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function createConfig($yamlConfigPath)
    {
        $yamlValidationIsArrayObserver = new YamlValueIsArrayValidationObserver(
            new ValueIsArray()
        );

        $this->file = new File(
            $yamlConfigPath,
            $yamlValidationIsArrayObserver
        );

        $commonMandatoryHeadersConfig = new CommonMandatoryHeaders($this->file, $yamlValidationIsArrayObserver);
        $optionalHeadersConfig = new OptionalHeaders($this->file, $yamlValidationIsArrayObserver);

        $fileHeadersConfig = new FileHeadersConfig(
            $optionalHeadersConfig,
            $commonMandatoryHeadersConfig
        );

        $this->validThatFileHeadersValuesAreUnique($fileHeadersConfig);

        return $fileHeadersConfig;
    }

    /**
     * @param FileHeadersConfig $fileHeadersConfig
     * @throws YamlValueAreDuplicatedException
     * @throws \Hj\Exception\KeyNotExist
     */
    private function validThatFileHeadersValuesAreUnique(FileHeadersConfig $fileHeadersConfig)
    {
        $values = [];

        foreach ($fileHeadersConfig->getCommonMandatoryHeadersConfig()->getValue() as $commonMandatoryHeader) {
            array_push($values, $commonMandatoryHeader);
        }
        foreach ($fileHeadersConfig->getOptionalHeadersConfig()->getValue() as $optionalHeader) {
            array_push($values, $optionalHeader);
        }

        // if the value appears more than once we throw an exception
        if (count($values) != count(array_unique($values)) ) {
            throw new YamlValueAreDuplicatedException("The '{$this->file->getKeyLabelUsedToRetrieveData()}' key has duplicated values. Only unique values are permitted. Please check your config file on '{$this->file->getKeyLabelUsedToRetrieveData()}' key");
        }
    }
}