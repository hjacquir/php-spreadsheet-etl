<?php
/**
 * User: h.jacquir
 * Date: 30/01/2020
 * Time: 15:42
 */

namespace Hj\Strategy\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Hj\Directory\Directory;
use Hj\Error\Database\DatabaseConnexionError;
use Hj\Exception\AttributeNotSetException;
use Hj\Helper\CatchedErrorHandler;
use Hj\Strategy\Strategy;
use Hj\YamlConfigLoader;

/**
 * Class InitializeEntityManagerStrategy
 * @package Hj\Strategy\Database
 */
class InitializeEntityManagerStrategy implements Strategy
{
    const DRIVER = 'driver';
    const HOST = 'host';
    const CHARSET = 'charset';
    const USER = 'user';
    const PASSWORD = 'password';
    const DBNAME = 'dbname';
    const PORT = 'port';

    /**
     * @var EntityManager
     */
    private $doctrineOrmEntityManager = null;

    /**
     * @var string
     */
    private $annotationXmlPath;

    /**
     * @var bool
     */
    private $autoGenerateProxyClasses;

    /**
     * @var YamlConfigLoader
     */
    private $configLoader;

    /**
     * @var DatabaseConnexionError
     */
    private $databaseError;

    /**
     * @var string
     */
    private $proxyDirPath;

    /**
     * @var Directory
     */
    private $waitingDirectory;

    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    /**
     * @var bool
     */
    private $isInitialized = false;

    /**
     * InitializeEntityManagerStrategy constructor.
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param string $annotationXmlPath
     * @param string $proxyDirPath
     * @param bool $autoGenerateProxyClasses
     * @param YamlConfigLoader $configLoader
     * @param DatabaseConnexionError $databaseError
     * @param Directory $waitingDirectory
     */
    public function __construct(
        CatchedErrorHandler $catchedErrorHandler,
        $annotationXmlPath,
        $proxyDirPath,
        $autoGenerateProxyClasses,
        YamlConfigLoader $configLoader,
        DatabaseConnexionError $databaseError,
        Directory $waitingDirectory
    )
    {
        $this->annotationXmlPath = $annotationXmlPath;
        $this->autoGenerateProxyClasses = $autoGenerateProxyClasses;
        $this->proxyDirPath = $proxyDirPath;
        $this->configLoader = $configLoader;
        $this->databaseError = $databaseError;
        $this->waitingDirectory = $waitingDirectory;
        $this->catchedErrorHandler = $catchedErrorHandler;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->waitingDirectory->hasFiles()
            && false === $this->catchedErrorHandler->getErrorCollector()->hasError();
    }

    public function apply()
    {
        if (is_null($this->doctrineOrmEntityManager)) {
            $config = Setup::createXMLMetadataConfiguration(array($this->annotationXmlPath));
            $config->setProxyDir($this->proxyDirPath);
            $config->setAutoGenerateProxyClasses($this->autoGenerateProxyClasses);

            $connexion = [
                self::DRIVER => $this->configLoader->getDatabaseDriver(),
                self::HOST => $this->configLoader->getDatabaseHost(),
                self::CHARSET => $this->configLoader->getDatabaseCharset(),
                self::USER => $this->configLoader->getDatabaseUser(),
                self::PASSWORD => $this->configLoader->getDatabasePassword(),
                self::DBNAME => $this->configLoader->getDatabaseDbName(),
                self::PORT => $this->configLoader->getDatabasePort(),
            ];

            try {
                $this->doctrineOrmEntityManager = EntityManager::create($connexion, $config);
                $this->doctrineOrmEntityManager->getConnection()->connect();
                $this->isInitialized = true;
            } catch (\Exception $e) {
                $this->catchedErrorHandler->handleErrorWhenDatabaseConnexionErrorOccurred($e, $this->databaseError);
            }
        }
    }

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->isInitialized;
    }

    /**
     * @return EntityManager
     * @throws AttributeNotSetException
     */
    public function getDoctrineOrmEntityManager()
    {
        $currentClass = get_class($this);

        if (is_null($this->doctrineOrmEntityManager)) {
            throw new AttributeNotSetException("The entity manager is not initialized. You need to call the {$currentClass} apply() method.");
        }

        return $this->doctrineOrmEntityManager;
    }
}