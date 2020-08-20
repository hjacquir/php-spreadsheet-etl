<?php
/**
 * User: h.jacquir
 * Date: 26/02/2020
 * Time: 13:49
 */

namespace Hj\Strategy\Data;

use Hj\Collector\ErrorCollector;
use Hj\Config\FileHeadersConfig;
use Hj\Error\ConfigFileMismatchError;
use Hj\File\Field\AbstractField;
use Hj\Strategy\Strategy;
use Hj\YamlConfigLoader;

/**
 * Compare file mandatory header defined in config file with field defined for extraction
 *
 * Class CheckFieldConfigStrategy
 * @package Hj\Strategy\Data
 */
class CheckFieldConfigStrategy implements Strategy
{
    /**
     * @var YamlConfigLoader
     */
    private $configLoader;

    /**
     * @var AbstractField[]
     */
    private $fields;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var ConfigFileMismatchError
     */
    private $associatedError;

    /**
     * @var FileHeadersConfig
     */
    private $fileHeadersConfig;

    /**
     * CheckFieldConfigStrategy constructor.
     * @param FileHeadersConfig $fileHeadersConfig
     * @param YamlConfigLoader $configLoader
     * @param AbstractField[] $fields
     * @param ErrorCollector $errorCollector
     * @param ConfigFileMismatchError $associatedError
     */
    public function __construct(
        FileHeadersConfig $fileHeadersConfig,
        YamlConfigLoader $configLoader,
        array $fields,
        ErrorCollector $errorCollector,
        ConfigFileMismatchError $associatedError
    )
    {
        $this->fileHeadersConfig = $fileHeadersConfig;
        $this->configLoader = $configLoader;
        $this->fields = $fields;
        $this->errorCollector = $errorCollector;
        $this->associatedError = $associatedError;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return true;
    }

    /**
     * @throws \Hj\Exception\KeyNotExist
     */
    public function apply()
    {
        $commonMandatoryHeaders = $this->fileHeadersConfig->getCommonMandatoryHeadersConfig()->getValue();
        $optionalHeaders = $this->fileHeadersConfig->getOptionalHeadersConfig()->getValue();

        $expectedConfigHeaders = array_merge(
            $commonMandatoryHeaders,
            $optionalHeaders
        );

        $fieldExpectedHeaders = [];

        foreach ($this->fields as $field) {
            array_push($fieldExpectedHeaders, $field->getExpectedHeaderValue());
        }

        $mismatchedHeader = [];

        foreach ($expectedConfigHeaders as $expectedConfigHeader) {
            if (!in_array($expectedConfigHeader, $fieldExpectedHeaders)) {
                array_push($mismatchedHeader, $expectedConfigHeader);
            }
        }

        if (count($mismatchedHeader) > 0) {
            $this->associatedError->setMismatchedKeys($mismatchedHeader);
            $this->errorCollector->addError($this->associatedError);
        }
    }
}