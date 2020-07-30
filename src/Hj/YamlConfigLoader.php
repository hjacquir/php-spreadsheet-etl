<?php
/**
 * User: h.jacquir
 * Date: 20/01/2020
 * Time: 16:02
 */

namespace Hj;

use Hj\Validator\Validator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlConfigLoader
 * @package Hj
 */
class YamlConfigLoader
{
    const KEY_FILE_PATH = 'filePath';
    const KEY_ARCHIVED = 'archived';
    const KEY_IN_PROCESSING = 'in_processing';
    const KEY_WAITING = 'waiting';
    const KEY_FILEPATH_FAILURE = 'failure';
    const KEY_SMTP = 'smtp';
    const KEY_SMTP_HOST = 'host';
    const KEY_FTP = 'ftp';
    const KEY_FTP_HOST = 'host';
    const KEY_FTP_USERNAME = 'username';
    const KEY_FTP_PASSWORD = 'password';
    const KEY_FTP_DIRECTORY = 'directory';
    const KEY_FTP_PORT = 'port';
    const KEY_MAILS = 'mails';
    const KEY_MAILS_ADMINS = 'admins';
    const KEY_MAILS_USERS = 'users';
    const KEY_MAILS_FROM = 'from';
    const KEY_FILE = 'file';
    const KEY_FILE_COMMON_MANDATORY_HEADERS = 'commonMandatoryHeaders';
    const KEY_FILE_OPTIONAL_HEADERS = 'optionalHeaders';
    const KEY_DATABASE = 'database';
    const KEY_DATABASE_DRIVER = 'driver';
    const KEY_DATABASE_HOST = 'host';
    const KEY_DATABASE_CHARSET = 'charset';
    const KEY_DATABASE_USER = 'user';
    const KEY_DATABASE_PASSWORD = 'password';
    const KEY_DATABASE_DBNAME = 'dbname';
    const KEY_DATABASE_PORT = 'port';

    /**
     * Path to the yaml config file
     *
     * @var string
     */
    private $yamlFile;

    /**
     * @var array
     */
    private $parsedValues;

    /**
     * YamlConfigLoader constructor.
     *
     * @param string $yamlFile
     * @param Validator $validator
     */
    public function __construct($yamlFile, Validator $validator)
    {
        $this->yamlFile = $yamlFile;
        $this->parsedValues = Yaml::parseFile($this->yamlFile);

        $validator->valid($this->parsedValues);
    }

    /**
     * @return string
     */
    public function getDatabaseDbName()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_DBNAME];
    }

    /**
     * @return string
     */
    public function getDatabasePort()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_PORT];
    }

    /**
     * @return string
     */
    public function getDatabasePassword()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_PASSWORD];
    }

    /**
     * @return string
     */
    public function getDatabaseUser()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_USER];
    }

    /**
     * @return string
     */
    public function getDatabaseCharset()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_CHARSET];
    }

    /**
     * @return string
     */
    public function getDatabaseHost()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_HOST];
    }

    /**
     * @return string
     */
    public function getDatabaseDriver()
    {
        return $this->parsedValues[self::KEY_DATABASE][self::KEY_DATABASE_DRIVER];
    }

    /**
     * @return array
     */
    public function getFileOptionalHeaders()
    {
        return $this->parsedValues[self::KEY_FILE][self::KEY_FILE_OPTIONAL_HEADERS];
    }

    /**
     * @return array
     */
    public function getFileCommonMandatoryHeaders()
    {
        return $this->parsedValues[self::KEY_FILE][self::KEY_FILE_COMMON_MANDATORY_HEADERS];
    }

    /**
     * @return string
     */
    public function getMailFrom()
    {
        return $this->parsedValues[self::KEY_MAILS][self::KEY_MAILS_FROM];
    }

    /**
     * @return array
     */
    public function getAdminsMails()
    {
        return $this->parsedValues[self::KEY_MAILS][self::KEY_MAILS_ADMINS];
    }

    /**
     * @return array
     */
    public function getUsersMails()
    {
        return $this->parsedValues[self::KEY_MAILS][self::KEY_MAILS_USERS];
    }

    /**
     * @return string
     */
    public function getArchivedFilePath()
    {
        return $this->parsedValues[self::KEY_FILE_PATH][self::KEY_ARCHIVED];
    }

    /**
     * @return string
     */
    public function getInProcessingFilePath()
    {
        return $this->parsedValues[self::KEY_FILE_PATH][self::KEY_IN_PROCESSING];
    }

    /**
     * @return string
     */
    public function getWaitingFilePath()
    {
        return $this->parsedValues[self::KEY_FILE_PATH][self::KEY_WAITING];
    }

    /**
     * @return string
     */
    public function getFailureFilePath()
    {
        return $this->parsedValues[self::KEY_FILE_PATH][self::KEY_FILEPATH_FAILURE];
    }

    /**
     * @return string
     */
    public function getSmtpHost()
    {
        return $this->parsedValues[self::KEY_SMTP][self::KEY_SMTP_HOST];
    }

    /**
     * @return string
     */
    public function getFtpHost()
    {
        return $this->parsedValues[self::KEY_FTP][self::KEY_FTP_HOST];
    }

    /**
     * @return string
     */
    public function getFtpUsername()
    {
        return $this->parsedValues[self::KEY_FTP][self::KEY_FTP_USERNAME];
    }

    /**
     * @return string
     */
    public function getFtpPassword()
    {
        return $this->parsedValues[self::KEY_FTP][self::KEY_FTP_PASSWORD];
    }

    /**
     * @return string
     */
    public function getFtpDirectory()
    {
        return $this->parsedValues[self::KEY_FTP][self::KEY_FTP_DIRECTORY];
    }

    /**
     * @return string
     */
    public function getFtpPort()
    {
        return $this->parsedValues[self::KEY_FTP][self::KEY_FTP_PORT];
    }
}