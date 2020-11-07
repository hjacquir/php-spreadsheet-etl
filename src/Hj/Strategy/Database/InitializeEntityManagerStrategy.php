<?php
/**
 * User: h.jacquir
 * Date: 30/01/2020
 * Time: 15:42
 */

namespace Hj\Strategy\Database;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Hj\Config\DatabaseConfig;
use Hj\Directory\Directory;
use Hj\Error\Database\DatabaseConnexionError;
use Hj\Exception\AttributeNotSetException;
use Hj\Helper\CatchedErrorHandler;
use Hj\Strategy\Strategy;

/**
 * Class InitializeEntityManagerStrategy
 * @package Hj\Strategy\Database
 */
class InitializeEntityManagerStrategy implements Strategy
{
    /**
     * @var EntityManager|null
     */
    private ?EntityManager $doctrineOrmEntityManager = null;

    /**
     * @var string
     */
    private string $annotationXmlPath;

    /**
     * @var bool
     */
    private bool $autoGenerateProxyClasses;

    /**
     * @var DatabaseConnexionError
     */
    private DatabaseConnexionError $databaseError;

    /**
     * @var string
     */
    private string $proxyDirPath;

    /**
     * @var Directory
     */
    private Directory $waitingDirectory;

    /**
     * @var CatchedErrorHandler
     */
    private CatchedErrorHandler $catchedErrorHandler;

    /**
     * @var bool
     */
    private bool $isInitialized = false;

    /**
     * @var DatabaseConfig
     */
    private DatabaseConfig $databaseConfig;

    /**
     * InitializeEntityManagerStrategy constructor.
     * @param DatabaseConfig $databaseConfig
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param string $annotationXmlPath
     * @param string $proxyDirPath
     * @param bool $autoGenerateProxyClasses
     * @param DatabaseConnexionError $databaseError
     * @param Directory $waitingDirectory
     */
    public function __construct(
        DatabaseConfig $databaseConfig,
        CatchedErrorHandler $catchedErrorHandler,
        string $annotationXmlPath,
        string $proxyDirPath,
        bool $autoGenerateProxyClasses,
        DatabaseConnexionError $databaseError,
        Directory $waitingDirectory
    )
    {
        $this->databaseConfig = $databaseConfig;
        $this->annotationXmlPath = $annotationXmlPath;
        $this->autoGenerateProxyClasses = $autoGenerateProxyClasses;
        $this->proxyDirPath = $proxyDirPath;
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

    /**
     * @throws DBALException
     */
    public function apply()
    {
        if (is_null($this->doctrineOrmEntityManager)) {
            $config = Setup::createXMLMetadataConfiguration(
                [
                    $this->annotationXmlPath,
                ]
            );
            $config->setProxyDir($this->proxyDirPath);
            $config->setAutoGenerateProxyClasses($this->autoGenerateProxyClasses);

            $connectionParameters = [
                'url' => $this->databaseConfig->getUrl()->getValue(),
            ];

            $connexion = DriverManager::getConnection($connectionParameters);

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