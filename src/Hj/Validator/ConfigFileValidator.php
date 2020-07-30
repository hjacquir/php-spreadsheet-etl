<?php

namespace Hj\Validator;

use Hj\Exception\YamlKeyNotDefined;
use Hj\Exception\YamlValueAreDuplicatedException;
use Hj\Exception\YamlValueWrongFormat;
use Hj\YamlConfigLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigFileValidator
 * @package Hj\Validator
 * @todo add tests
 */
class ConfigFileValidator implements Validator
{
    /**
     * @var string
     */
    private $yamlFilePath;

    /**
     * @var array
     */
    private $parsedValues = [];

    /**
     * KeyValueValidator constructor.
     * @param string $yamlFilePath
     */
    public function __construct($yamlFilePath)
    {
        $this->yamlFilePath = $yamlFilePath;
        $this->parsedValues = Yaml::parse($this->yamlFilePath);
    }

    /**
     * @return string
     */
    public function getYamlFilePath()
    {
        return $this->yamlFilePath;
    }

    /**
     * @return array
     */
    public function getParsedValues()
    {
        return $this->parsedValues;
    }

    /**
     * @param array $value
     * @throws YamlKeyNotDefined
     * @throws YamlValueAreDuplicatedException
     * @throws YamlValueWrongFormat
     */
    public function valid($value)
    {
        // 'filePath'
        $this->validKey($value, YamlConfigLoader::KEY_FILE_PATH);
        $this->valueIsArray($value[YamlConfigLoader::KEY_FILE_PATH], YamlConfigLoader::KEY_FILE_PATH);
        $this->valueIsString($value[YamlConfigLoader::KEY_FILE_PATH][YamlConfigLoader::KEY_ARCHIVED], YamlConfigLoader::KEY_ARCHIVED);
        $this->valueIsString($value[YamlConfigLoader::KEY_FILE_PATH][YamlConfigLoader::KEY_IN_PROCESSING], YamlConfigLoader::KEY_IN_PROCESSING);
        $this->valueIsString($value[YamlConfigLoader::KEY_FILE_PATH][YamlConfigLoader::KEY_WAITING], YamlConfigLoader::KEY_WAITING);
        $this->valueIsString($value[YamlConfigLoader::KEY_FILE_PATH][YamlConfigLoader::KEY_FILEPATH_FAILURE], YamlConfigLoader::KEY_FILEPATH_FAILURE);

        // 'smtp'
        $this->validKey($value, YamlConfigLoader::KEY_SMTP);
        $this->valueIsArray($value[YamlConfigLoader::KEY_SMTP], YamlConfigLoader::KEY_SMTP);
        $this->valueIsString($value[YamlConfigLoader::KEY_SMTP][YamlConfigLoader::KEY_SMTP_HOST], YamlConfigLoader::KEY_SMTP_HOST);

        // 'ftp'
        $this->validKey($value, YamlConfigLoader::KEY_FTP);
        $this->valueIsArray($value[YamlConfigLoader::KEY_FTP], YamlConfigLoader::KEY_FTP);
        $this->valueIsString($value[YamlConfigLoader::KEY_FTP][YamlConfigLoader::KEY_FTP_HOST], YamlConfigLoader::KEY_FTP_HOST);
        $this->valueIsString($value[YamlConfigLoader::KEY_FTP][YamlConfigLoader::KEY_FTP_USERNAME], YamlConfigLoader::KEY_FTP_USERNAME);
        $this->valueIsString($value[YamlConfigLoader::KEY_FTP][YamlConfigLoader::KEY_FTP_PASSWORD], YamlConfigLoader::KEY_FTP_PASSWORD);
        $this->valueIsString($value[YamlConfigLoader::KEY_FTP][YamlConfigLoader::KEY_FTP_DIRECTORY], YamlConfigLoader::KEY_FTP_DIRECTORY);
        $this->valueIsString($value[YamlConfigLoader::KEY_FTP][YamlConfigLoader::KEY_FTP_PORT], YamlConfigLoader::KEY_FTP_PORT);

        // 'mails'
        $this->validKey($value, YamlConfigLoader::KEY_MAILS);
        $this->valueIsArray($value[YamlConfigLoader::KEY_MAILS], YamlConfigLoader::KEY_MAILS);
        $this->valueIsArray($value[YamlConfigLoader::KEY_MAILS][YamlConfigLoader::KEY_MAILS_ADMINS], YamlConfigLoader::KEY_MAILS_ADMINS);
        $this->valueIsArray($value[YamlConfigLoader::KEY_MAILS][YamlConfigLoader::KEY_MAILS_USERS], YamlConfigLoader::KEY_MAILS_USERS);
        $this->valueIsString($value[YamlConfigLoader::KEY_MAILS][YamlConfigLoader::KEY_MAILS_FROM], YamlConfigLoader::KEY_MAILS_FROM);

        // 'file'
        $this->validKey($value, YamlConfigLoader::KEY_FILE);
        $this->valueIsArray($value[YamlConfigLoader::KEY_FILE], YamlConfigLoader::KEY_FILE);
        $this->valueIsArray($value[YamlConfigLoader::KEY_FILE][YamlConfigLoader::KEY_FILE_COMMON_MANDATORY_HEADERS], YamlConfigLoader::KEY_FILE_COMMON_MANDATORY_HEADERS);
        $this->valueIsArray($value[YamlConfigLoader::KEY_FILE][YamlConfigLoader::KEY_FILE_OPTIONAL_HEADERS], YamlConfigLoader::KEY_FILE_OPTIONAL_HEADERS);

        $this->validThatFileHeadersValuesAreUnique($value);

        // 'database'
        $this->validKey($value, YamlConfigLoader::KEY_DATABASE);
        $this->valueIsArray($value[YamlConfigLoader::KEY_DATABASE], YamlConfigLoader::KEY_DATABASE);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_DRIVER], YamlConfigLoader::KEY_DATABASE_DRIVER);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_HOST], YamlConfigLoader::KEY_DATABASE_HOST);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_CHARSET], YamlConfigLoader::KEY_DATABASE_CHARSET);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_USER], YamlConfigLoader::KEY_DATABASE_USER);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_PASSWORD], YamlConfigLoader::KEY_DATABASE_PASSWORD);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_DBNAME], YamlConfigLoader::KEY_DATABASE_DBNAME);
        $this->valueIsString($value[YamlConfigLoader::KEY_DATABASE][YamlConfigLoader::KEY_DATABASE_PORT], YamlConfigLoader::KEY_DATABASE_PORT);
    }

    /**
     * @param array $array
     * @param string $keyName
     * @throws YamlKeyNotDefined
     */
    private function validKey(array $array, $keyName)
    {
        if (!isset($array[$keyName])) {
            throw new YamlKeyNotDefined("Wrong yaml file configuration in : '{$this->yamlFilePath}'. The key '{$keyName}' is not defined. Please check your yaml file and define it.");
        }
    }

    /**
     * @param $value
     * @param string $key
     * @throws YamlValueWrongFormat
     */
    private function valueIsArray($value, $key)
    {
        if (!is_array($value)) {
            throw new YamlValueWrongFormat("Wrong yaml file configuration in : '{$this->yamlFilePath}'. The value for the key : '{$key}' must be an array. Please check your yaml file.");
        }
    }

    /**
     * @param string $value
     * @param string $key
     * @throws YamlValueWrongFormat
     */
    private function valueIsString($value, $key)
    {
        if (!is_string($value)) {
            throw new YamlValueWrongFormat("Wrong yaml file configuration in : '{$this->yamlFilePath}'. The value for the key : '{$key}' must be an string. Please check your yaml file.");
        }

    }

    /**
     * Valid that 'file' key do not have duplicated values
     *
     * @param $value
     * @throws YamlValueAreDuplicatedException
     */
    private function validThatFileHeadersValuesAreUnique($value)
    {
        $values = [];

        $commonMandatoryHeaders = $value[YamlConfigLoader::KEY_FILE][YamlConfigLoader::KEY_FILE_COMMON_MANDATORY_HEADERS];

        foreach ($commonMandatoryHeaders as $commonMandatoryHeader) {
            array_push($values, $commonMandatoryHeader);
        }

        $optionalHeaders = $value[YamlConfigLoader::KEY_FILE][YamlConfigLoader::KEY_FILE_OPTIONAL_HEADERS];

        foreach ($optionalHeaders as $optionalHeader) {
            array_push($values, $optionalHeader);
        }

        $unique = array_unique($values);

        if (count($values) != count($unique) ) {
            throw new YamlValueAreDuplicatedException("The 'file' key has duplicated values. Only unique values are permitted. Please check your config file on 'file' key");
        }
    }
}