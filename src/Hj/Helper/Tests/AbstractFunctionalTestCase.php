<?php
/**
 * User: h.jacquir
 * Date: 12/03/2020
 * Time: 14:20
 */

namespace Hj\Helper\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Hj\Command\AbstractCommand;
use Hj\Error\Error;
use Hj\Exception\KeyNotExist;
use Hj\Exception\WrongTypeException;
use Hj\Factory\DatabaseConfigFactory;
use Hj\Validator\ConfigFileValidator;
use Monolog\Logger;
use Nelmio\Alice\Loader\NativeLoader;
use PHPUnit\Framework\MockObject\MockObject;
use Yalesov\FileSystemManager\FileSystemManager;

/**
 * Class AbstractFunctionalTestCase
 * @package Hj\Helper\Tests
 */
abstract class AbstractFunctionalTestCase extends AbstractTestCase
{
    const FAKE_SMTP_SERVER_RECEIVED_MAILS_FOLDER_PATH = "/../../fake_smtp_server/received-mails/";
    const TEST_FILES_FOLDER_PATH = "/../../testFilesFolder/";
    const DISTANT_FOLDER_PATH = "C:/for-spreadsheet-etl-tests/";
    const YAML_CONTEXT_FILE_PATH = "yamlContextFilePath";
    const CONFIG_FILES_SAMPLES_PATH = "/../../config_files_samples/";

    /**
     * @var Logger|MockObject
     */
    private $loggerMock;

    /**
     * @var Logger
     */
    private $realLogger;

    /**
     * @return Logger
     */
    protected function getRealLogger(): Logger
    {
        return $this->realLogger;
    }


    /**
     * @var string
     */
    private $emailsFolder;

    /**
     * @var string
     */
    private $testFilesFolder;

    /**
     * @var string
     */
    private $distantFolderForTest;

    /**
     * @var EntityManager
     */
    private $entityManager = null;

    /**
     * @var array
     */
    private $entities = [];


    public function setUp()
    {
        $this->init();
        // we use a mock of Logger when we do not to send mail when error occured
        $this->loggerMock = $this->getMockConstructorDisabled(Logger::class);
        // we use the real logger when we need to test sending email
        $this->realLogger = new Logger("console");
        // before each test we reset the distant folder
        FileSystemManager::rrmdir($this->distantFolderForTest);
        // copy base structure folder
        FileSystemManager::rcopy($this->testFilesFolder . "base/", $this->distantFolderForTest);
        // remove waiting, in processing folder by removing the file needed for commit
        FileSystemManager::rrmdir($this->distantFolderForTest . "waiting");
        FileSystemManager::rrmdir($this->distantFolderForTest . "in_processing");
        // and recreate them empty
        mkdir($this->distantFolderForTest . "waiting");
        mkdir($this->distantFolderForTest . "in_processing");
        // copy local sqlite database from test folder to distant folder test
        FileSystemManager::rcopy(
            $this->testFilesFolder . "/../test.sqlite",
            $this->distantFolderForTest  . "/test.sqlite");

        $connection = $this
            ->getEntityManager()
            ->getConnection();
        $this->truncateDatabase($connection);
        $this->insertFixtures();
    }

    /**
     * @param string $fileName The original file name
     * @param int $expectedIndexFile The expectedIndexFile is 1 by default because we ignore the index 0 that corresponding the the file .only-for-commit
     */
    protected function assertThatFileWasMovedToTheFailureFolder($fileName, $expectedIndexFile = 1)
    {
        $files = FileSystemManager::fileIterator($this->getDistantFolderForTest() . '/failure');
        $currentFileName = $files[$expectedIndexFile];
        self::assertContains($fileName, $currentFileName, "An error occured the file was not moved to the failure folder");
    }

    /**
     * @param string $currentErrorClass
     * @param AbstractCommand $command
     */
    public function assertThatCurrentAdminErrorIsInstanceOf($currentErrorClass, AbstractCommand $command)
    {
        $errors = $command->getErrorCollector()->getAllAdminErrors();

        self::assertInstanceOf(
            $currentErrorClass,
            current($errors),
            "The currently ADMIN error returned does not correspond to that expected"
        );
    }

    /**
     * @param string $fileName The original file name
     * @param int $expectedIndexFile The expectedIndexFile is 1 by default because we ignore the index 0 that corresponding the the file .only-for-commit
     */
    protected function assertThatFileWasMovedToTheArchivedFolder($fileName, $expectedIndexFile = 1)
    {
        $files = FileSystemManager::fileIterator($this->getDistantFolderForTest() . '/archived');
        $currentFileName = $files[$expectedIndexFile];
        self::assertContains($fileName, $currentFileName, "An error occured the file was not moved to the archived folder");
    }

    /**
     * @param $expectedUserErrorCount
     * @param AbstractCommand $command
     */
    public function assertThatNumberOfUserErrorIsAsExpected($expectedUserErrorCount, AbstractCommand $command)
    {
        $errors = $command->getErrorCollector()->getAllUserErrors();

        self::assertSame(
            $expectedUserErrorCount,
            count($errors),
            "The number of USER error thrown is not as expected."
        );
    }

    /**
     * @param int $expectedAdminErrorCount
     * @param AbstractCommand $command
     */
    public function assertThatNumberOfAdminErrorIsAsExpected($expectedAdminErrorCount, AbstractCommand $command)
    {
        $errors = $command->getErrorCollector()->getAllAdminErrors();

        self::assertSame(
            $expectedAdminErrorCount,
            count($errors),
            "The number of ADMIN error thrown is not as expected."
        );
    }

    /**
     * @param string $fileName The original file name
     * @param int $expectedIndexFile
     */
    protected function assertThatFileWasNotDeletedFromWaitingFolder($fileName, $expectedIndexFile = 0)
    {
        $files = FileSystemManager::fileIterator($this->getDistantFolderForTest() . '/waiting');
        $currentFileName = $files[$expectedIndexFile];
        self::assertContains($fileName, $currentFileName, "An error occured the file does not exist to the waiting folder");
    }

    protected function assertThatWaitingFolderIsEmpty()
    {
        $files = FileSystemManager::fileIterator($this->getDistantFolderForTest() . '/waiting');
        self::assertEmpty($files, "The waiting folder is not empty");
    }

    /**
     * @param string $errorClassName The class error class name expected
     * @param $errorTarget
     * @param AbstractCommand $command
     */
    protected function assertThatErrorHasThrownAndEmailSent(
        $errorClassName,
        $errorTarget,
        AbstractCommand $command
    ) {
        $errors = [];

        if ($errorTarget === Error::TARGET_USER) {
            $errors = $command->getErrorCollector()->getAllUserErrors();
        }

        if ($errorTarget === Error::TARGET_ADMIN) {
            $errors = $command->getErrorCollector()->getAllAdminErrors();
        }


        self::assertSame(
            1,
            count($errors),
            "An error should have been returned. In this case, no error was returned."
        );
        self::assertInstanceOf(
            $errorClassName,
            current($errors),
            "The error currently returned does not correspond to that expected"
        );
        $this->assertThatEmailWasSent();
    }

    private function init()
    {
        $this->setEmailsFolder(
            $this->getCurrentContextualDir() .
            self::FAKE_SMTP_SERVER_RECEIVED_MAILS_FOLDER_PATH
        );
        $this->setTestFilesFolder(
            $this->getCurrentContextualDir() .
            self::TEST_FILES_FOLDER_PATH
        );
        $this->setDistantFolderForTest(
            self::DISTANT_FOLDER_PATH
        );
    }

    /**
     * @param string $emailsFolder
     */
    private function setEmailsFolder(string $emailsFolder): void
    {
        $this->emailsFolder = $emailsFolder;
    }

    protected abstract function getCurrentContextualDir();

    /**
     * @param string $testFilesFolder
     */
    private function setTestFilesFolder(string $testFilesFolder): void
    {
        $this->testFilesFolder = $testFilesFolder;
    }

    /**
     * @param string $distantFolderForTest
     */
    private function setDistantFolderForTest(string $distantFolderForTest): void
    {
        $this->distantFolderForTest = $distantFolderForTest;
    }

    /**
     * @param Connection $connection
     * @throws DBALException
     */
    private function truncateDatabase(Connection $connection)
    {
        $schemaManager = $connection->getSchemaManager();

        $tables = $schemaManager->listTables();
        foreach ($tables as $table) {
            $name = $table->getName();
            $connection->executeQuery('DELETE FROM ' . $name . ';');
        }
    }

    /**
     * @return EntityManager
     * @throws ORMException
     * @throws KeyNotExist
     * @throws WrongTypeException
     * @throws DBALException
     */
    protected function getEntityManager()
    {
        if (is_null($this->entityManager)) {
            $config = Setup::createXMLMetadataConfiguration(array($this->getCurrentContextualDir() . "/../../../doctrine"));
            $config->setProxyDir($this->getCurrentContextualDir() . "/../../../doctrineProxies");
            $config->setAutoGenerateProxyClasses(true);

            $databaseConfigs = (new DatabaseConfigFactory())->createConfig(
                $this->getTestConfigFileFolderPath() . "configFileWithoutError.yaml"
            );

            $connectionParameters = array(
                'url' => $databaseConfigs->getUrl()->getValue(),
            );

            $connexion = DriverManager::getConnection($connectionParameters);

            $this->entityManager = EntityManager::create($connexion, $config);
            $this->entityManager->getConnection()->connect();
            return $this->entityManager;
        }

        return $this->entityManager;
    }

    /**
     * @return string
     */
    protected function getTestConfigFileFolderPath()
    {
        return $this->getCurrentContextualDir() .
            self::CONFIG_FILES_SAMPLES_PATH;
    }

    private function insertFixtures()
    {
        $loader = new NativeLoader();
        $this->loadFixtureEntities($loader, 'fixtures.yaml');

        foreach ($this->entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param NativeLoader $loader
     * @param string $fileFixture
     */
    private function loadFixtureEntities(NativeLoader $loader, $fileFixture)
    {
        $objectSet = $loader->loadFile(
            $this->getCurrentContextualDir()
            . '/../fixtures/'
            . $fileFixture
        );

        foreach ($objectSet->getObjects() as $entity) {
            array_push($this->entities, $entity);
        }
    }

    public function tearDown()
    {
        $connection = $this->getEntityManager()->getConnection();
        $this->truncateDatabase($connection);
        // after every tests we reset the email folder receiver
        $this->resetEmailFolders();
        // after each test we reset the distant folder
        FileSystemManager::rrmdir($this->distantFolderForTest);
    }

    public function resetEmailFolders()
    {
        FileSystemManager::rrmdir($this->emailsFolder);
        // recreate folder empty
        mkdir($this->emailsFolder);
    }

    /**
     * @return Logger|MockObject
     */
    protected function getLoggerMock()
    {
        return $this->loggerMock;
    }

    protected function assertThatEmailWasSent()
    {
        $files = FileSystemManager::fileIterator($this->emailsFolder);
        self::assertNotEmpty($files, "We expect that email was sent, but in this case email was not sent");
    }

    /**
     * @param int $expectedNumberOfSendedMails
     */
    protected function assertCountSendedMail($expectedNumberOfSendedMails)
    {
        $files = FileSystemManager::fileIterator($this->emailsFolder);
        $actualMailSended = count($files);
        self::assertEquals($expectedNumberOfSendedMails, $actualMailSended, "We expecte {$expectedNumberOfSendedMails} sended, but in this case {$actualMailSended} are sended.");
    }

    protected function assertThatNoEmailWasSent()
    {
        $files = FileSystemManager::fileIterator($this->emailsFolder);
        self::assertEmpty($files, "We expect that email was not sent, but in this case email was sent");
    }

    /**
     * @param string $errorTarget
     * @param AbstractCommand $command
     */
    protected function assertThatErrorDoNotThrown(
        $errorTarget,
        AbstractCommand $command
    ) {
        $errors = [];

        if ($errorTarget === Error::TARGET_USER) {
            $errors = $command->getErrorCollector()->getAllUserErrors();
        }

        if ($errorTarget === Error::TARGET_ADMIN) {
            $errors = $command->getErrorCollector()->getAllAdminErrors();
        }

        self::assertSame(
            0,
            count($errors),
            "No error is expected to be returned in this case."
        );
    }

    /**
     * @param string $localWaitingFolderName
     */
    protected function copyContextualTestFileWaitingToDistantWaitingFolder(
        $localWaitingFolderName
    ) {
        FileSystemManager::rcopy(
            $this->getTestFilesFolder() . $localWaitingFolderName,
            $this->getDistantFolderForTest() . "waiting/"
        );
    }

    /**
     * @return string
     */
    private function getTestFilesFolder(): string
    {
        return $this->testFilesFolder;
    }

    /**
     * @return string
     */
    protected function getDistantFolderForTest(): string
    {
        return $this->distantFolderForTest;
    }
}