<?php
/**
 * User: h.jacquir
 * Date: 02/03/2020
 * Time: 16:39
 */

namespace Hj\Tests\Functional\Command;

use Hj\Command\ExtractCommand;
use Hj\Command\ResetFlagAdminErrorOccured;
use Hj\Error\ConfigFileMismatchError;
use Hj\Error\Data\DataDateInvalidError;
use Hj\Error\Data\DataLengthReachedError;
use Hj\Error\Data\DataMandatoryMissingError;
use Hj\Error\Database\DatabaseConnexionError;
use Hj\Error\DuplicateHeaderError;
use Hj\Error\Error;
use Hj\Error\File\FileWithoutExtensionError;
use Hj\Error\FileExtensionError;
use Hj\Error\FileWithMultipleSheetsError;
use Hj\Error\HeaderNotOnFirstRowError;
use Hj\Error\MandatoryHeaderMissing;
use Hj\File\CellAdapter;
use Hj\File\Field\BirthDate;
use Hj\File\RowAdapter;
use Hj\Helper\Tests\AbstractFunctionalTestCase;
use Hj\Model\Person;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ExtractCommandTest
 *
 * @package Hj\Tests\Functional\Command
 * @covers  \Hj\Command\ExtractCommand
 * @covers  \Hj\Command\ResetFlagAdminErrorOccured
 */
class ExtractCommandTest extends AbstractFunctionalTestCase
{
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const BIRTH_DATE = 'birthDate';

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function testSequenceBehaviourFirstFileOccuredAdminErrorAndWeDoNotResetAdminFlagAndProcessSecondFileWithoutErrors()
    {
        // we have 1 data in database (the initial from fixtures)
        $this->assertCountDatasIntoDatabase(Person::class, 1);

        // launch command firstly with a file without errors but emule admin error with database connexion error
        $this->copyContextualTestFileWaitingToDistantWaitingFolder(
            'extractFromCsv'
        );

        $firstCommand = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . 'databaseError.yaml'
        );

        // we have 1 data in database (the initial from fixtures)
        $this->assertCountDatasIntoDatabase(Person::class, 1);

        // launch command secondly with the same config file with admin error and file without errors
        $this->copyContextualTestFileWaitingToDistantWaitingFolder(
            'extractFromXls'
        );
        $secondCommand = $this->launchExtractCommandWithRealLoggerWithoutFlagAdminErrorReseted(
            $this->getTestConfigFileFolderPath() . 'databaseError.yaml'
        );

        // we already have 1 data (the initial)
        $this->assertCountDatasIntoDatabase(Person::class, 1);
        // assert that admin error was thrown in the two cases
        $this->assertThatCurrentAdminErrorIsInstanceOf(DatabaseConnexionError::class, $firstCommand);
        $this->assertThatCurrentAdminErrorIsInstanceOf(DatabaseConnexionError::class, $secondCommand);

        // assert that the file are always in waiting folder
        $this->assertThatFileWasNotDeletedFromWaitingFolder('extractFromCsv');
        $this->assertThatFileWasNotDeletedFromWaitingFolder('extractFromXls', 1);

        // we assert that only one mail was sended for admin
        $this->assertCountSendedMail(1);
    }

    public function testSequenceBehaviourFirstFileOccuredAdminErrorAndWeResetAdminFlagAndProcessSecondFileWithoutErrors()
    {
        // we have 1 data in database (the initial from fixtures)
        $this->assertCountDatasIntoDatabase(Person::class, 1);

        // launch command firstly with a file without errors but emule admin error with database connexion error
        $this->copyContextualTestFileWaitingToDistantWaitingFolder(
            'extractFromOds'
        );
        $firstCommand = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . 'databaseError.yaml'
        );

        // we have 1 data in database (the initial from fixtures)
        $this->assertCountDatasIntoDatabase(Person::class, 1);

        // launch command secondly with a config file without error and a file without errors and reseting admin error
        $this->copyContextualTestFileWaitingToDistantWaitingFolder(
            'extractFromCsv'
        );
        $secondCommand = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . 'configFileWithoutError.yaml',
        );

        // we  have now 3 datas
        $this->assertCountDatasIntoDatabase(Person::class, 3);
        // assert that admin error was thrown in the first case
        $this->assertThatCurrentAdminErrorIsInstanceOf(DatabaseConnexionError::class, $firstCommand);

        // assert that the first file is always in waiting folder and the second into archived
        $this->assertThatFileWasNotDeletedFromWaitingFolder('extractFromOds');
        $this->assertThatFileWasMovedToTheArchivedFolder('extractFromCsv');

        // we assert that two mails was sended
        $this->assertCountSendedMail(2);
    }

    public function testSequenceLaunchCommandWithoutWaitingFileAndConfigFileWithError()
    {
        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . 'databaseError.yaml'
        );
        // waiting folder is empty
        $this->assertThatWaitingFolderIsEmpty();
        // no mail was sent
        $this->assertCountSendedMail(0);
        // we assert that no errors was thrown
        $this->assertThatNumberOfUserErrorIsAsExpected(0, $command);
        $this->assertThatNumberOfAdminErrorIsAsExpected(0, $command);
    }

    public function testSequenceLaunchCommandWithoutWaitingFileAndConfigFileWithoutError()
    {
        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . 'extractFromCsv.yaml'
        );
        // waiting folder is empty
        $this->assertThatWaitingFolderIsEmpty();
        // no mail was sent
        $this->assertCountSendedMail(0);
        // we assert that no errors was thrown
        $this->assertThatNumberOfUserErrorIsAsExpected(0, $command);
        $this->assertThatNumberOfAdminErrorIsAsExpected(0, $command);
    }

    /**
     * @return array
     */
    public function provideDataToTestingBehaviourWhenUserErrorOccured()
    {
        return [
            // data : first name length reached
            'firstNameLengthReached' => [
                "firstNameLengthReached.yaml",
                DataLengthReachedError::class,
                'firstNameLengthReached',
            ],
            // data : last name length reached
            'lastNameLengthReached' => [
                "lastNameLengthReached.yaml",
                DataLengthReachedError::class,
                'lastNameLengthReached',
            ],
            // data mandatory : first name missing
            'firstNameMissing' => [
                "firstNameMissing.yaml",
                DataMandatoryMissingError::class,
                'firstNameMissing',
            ],
            // data mandatory : last name missing
            'lastNameMissing' => [
                "lastNameMissing.yaml",
                DataMandatoryMissingError::class,
                'lastNameMissing',
            ],
            // data mandatory : birth date missing
            'birthDateMissing' => [
                "birthDateMissing.yaml",
                DataMandatoryMissingError::class,
                'birthDateMissing',
            ],
            // data birth date invalid format
            'birthDateInvalidFormat' => [
                "birthDateInvalidFormat.yaml",
                DataDateInvalidError::class,
                'birthDateInvalidFormat',
            ],
            // header not unique
            'headerDuplicated' => [
                "headerDuplicated.yaml",
                DuplicateHeaderError::class,
                'headerDuplicated',
            ],
            // common mandatory header missing
            'commonMandatoryHeaderMissing' => [
                "commonMandatoryHeaderMissing.yaml",
                MandatoryHeaderMissing::class,
                'commonMandatoryHeaderMissing',
            ],
            // file header not on first row
            'headerNotOnFirstRow' => [
                "headerNotOnFirstRow.yaml",
                HeaderNotOnFirstRowError::class,
                'headerNotOnFirstRow',
            ],
            // file with multiple sheets
            'fileWithMultipleSheets' => [
                "fileWithMultipleSheets.yaml",
                FileWithMultipleSheetsError::class,
                'fileWithMultipleSheets',
            ],
            // format not supported
            'formatNotSupported' => [
                "formatNotSupported.yaml",
                FileExtensionError::class,
                'formatNotSupported',
            ],
        ];
    }

    /**
     * @param string $configFile
     * @param string $classError
     * @param string $contextualFileForTest
     *
     * @dataProvider provideDataToTestingBehaviourWhenUserErrorOccured
     */
    public function testBehaviourOnUserError(
        $configFile,
        $classError,
        $contextualFileForTest
    )
    {
        $this->assertThatEmailWasSentAndErrorWasThrown(
            $configFile,
            $classError,
            Error::TARGET_USER,
            $contextualFileForTest
        );
        // assert that waiting folder is cleaned
        $this->assertThatWaitingFolderIsEmpty();
        // assert that file was movded to the failure folder
        $this->assertThatFileWasMovedToTheFailureFolder($contextualFileForTest);
    }

    /**
     * @return array
     */
    public function provideDataToTestingBehaviourWhenAdminErrorOccured()
    {
        return [
            // file without extension
            'fileWithoutExtension' => [
                "fileWithoutExtension.yaml",
                FileWithoutExtensionError::class,
                'fileWithoutExtension',
            ],
            // database connexion error
            'databaseError' => [
                "databaseError.yaml",
                DatabaseConnexionError::class,
                'databaseError',
            ],
            // config file defined header mismatch with field instantiated
            'configFileMismatched' => [
                "configFileMismatched.yaml",
                ConfigFileMismatchError::class,
                'configFileMismatched'
            ],
        ];
    }

    /**
     * @param string $configFile
     * @param string $classError
     * @param string $contextualFileForTest
     *
     * @dataProvider provideDataToTestingBehaviourWhenAdminErrorOccured
     */
    public function testBehaviourOnAdminError(
        $configFile,
        $classError,
        $contextualFileForTest
    )
    {
        $this->assertThatEmailWasSentAndErrorWasThrown(
            $configFile,
            $classError,
            Error::TARGET_ADMIN,
            $contextualFileForTest
        );
        // when admin error occured the file is not deleted from waiting directory
        $this->assertThatFileWasNotDeletedFromWaitingFolder($contextualFileForTest);
    }

    public function testIfAddedStrategiesIsEqualToInstantiateStrategies()
    {
        $this->copyContextualTestFileWaitingToDistantWaitingFolder("compareStrategies");

        $command = $this->launchExtractCommandWithMocckedLogger(
            $this->getTestConfigFileFolderPath() . "configFileWithoutError.yaml"
        );

        $instantiatedStrategies = $command->getInstantiatedStrategies();
        $addedStrategies = $command->getAddedStrategies();

        $message = "The number of strategies currently added to the extract command does not match the strategies instantiated." .
            " Please check your command if you have omitted to add strategies to the processor.";
        self::assertSame($instantiatedStrategies, $addedStrategies, $message);
        self::assertSame(count($instantiatedStrategies), count($addedStrategies), $message);
    }

    /**
     * @return array
     */
    public function dataProviderToTestExtractionHeaderOrRows()
    {
        return [
            'extractFromCsv' => [
                'extractFromCsv',
                'extractFromCsv.yaml',
            ],
            'extractFromXls' => [
                'extractFromXls',
                'extractFromXls.yaml',
            ],
            'extractFromXlsx' => [
                'extractFromXlsx',
                'extractFromXlsx.yaml',
            ],
            'extractFromOds' => [
                'extractFromOds',
                'extractFromOds.yaml',

            ],
        ];
    }

    /**
     * @param $waitingFolderName
     * @param $configFileName
     *
     * @dataProvider dataProviderToTestExtractionHeaderOrRows
     */
    public function testExtractHeader($waitingFolderName, $configFileName)
    {
        $this->copyContextualTestFileWaitingToDistantWaitingFolder($waitingFolderName);
        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . $configFileName
        );
        $extractedHeaderValues = $command
            ->getHeaderExtractionStrategy()
            ->getExtractedHeaderValues();

        $expectedHeader = [
            'FIRSTNAME',
            'LASTNAME',
            'BIRTHDATE',
        ];

        self::assertSame($expectedHeader, $extractedHeaderValues);
    }

    /**
     * @param $waitingFolderName
     * @param $configFileName
     *
     * @dataProvider dataProviderToTestExtractionHeaderOrRows
     */
    public function testExtractRow($waitingFolderName, $configFileName)
    {
        $this->copyContextualTestFileWaitingToDistantWaitingFolder($waitingFolderName);
        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . $configFileName
        );

        $rowCollector = $command
            ->getRowAdapterCollectorStrategy()
            ->getRowCollector();


        self::assertSame(2, count($rowCollector->getRows()), "The number of extracted row does not match");
        $currentCellNormalizedValues = [];

        while ($rowCollector->valid()) {
            /** @var RowAdapter $current */
            $current = $rowCollector->current();

            $cells = $current->getCells();
            /** @var  CellAdapter $cell */
            foreach ($cells as $cell) {
                $currentCellNormalizedValues[$rowCollector->key()][] = $cell->getNormalizedValue();
            }

            $rowCollector->next();
        }

        $expectedRows = [
            1 => [
                'JOHN',
                'DOE',
                '02/01/2020',
            ],
            2 => [
                'JANE',
                'DOE',
                '02/01/2021',
            ],
        ];

        self::assertSame($expectedRows, $currentCellNormalizedValues);
    }

    /**
     * @param string $waitingFolderName
     * @param string $configFileName
     *
     * @dataProvider dataProviderToTestExtractionHeaderOrRows
     */
    public function testSaveDatasOnDatabase(
        $waitingFolderName,
        $configFileName
    ) {
        $expectedDatas = [
            1 => [
                self::FIRST_NAME => "JOHN",
                self::LAST_NAME => "DOE",
                self::BIRTH_DATE => "02/01/2020",
            ],
            2 => [
                self::FIRST_NAME => "JANE",
                self::LAST_NAME => "DOE",
                self::BIRTH_DATE => "02/01/2021",
            ],
        ];

        $this->copyContextualTestFileWaitingToDistantWaitingFolder($waitingFolderName);
        // no datas in database before launching the command only the
        // first inserted by nelmio/alice on setup from fixtures.yaml file
        $this->assertCountDatasIntoDatabase(Person::class, 1);

        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . $configFileName
        );

        /** @var Person[] $datas */
        $datas = $this->assertCountDatasIntoDatabase(Person::class, 3);

        foreach ($datas as $key => $data) {
            // ignore the first inserted by
            // nelmio/alice on setup from fixtures.yaml on setup
            if ($key !== 0) {
                self::assertSame($expectedDatas[$key][self::FIRST_NAME], $data->getFirstName());
                self::assertSame($expectedDatas[$key][self::LAST_NAME], $data->getLastName());
                self::assertSame($expectedDatas[$key][self::BIRTH_DATE], $data->getBirthDate()->format(BirthDate::DATE_DATABASE_FORMAT));
            }
        }
    }

    /**
     * @param string $className
     * @param int $expectedDataCount
     * @return array
     */
    private function assertCountDatasIntoDatabase($className, $expectedDataCount)
    {
        $datas = $this->getEntityManager()
            ->getRepository($className)
            ->findAll();

        self::assertCount(
            $expectedDataCount,
            $datas,
            "The number of data is not as expected"
        );

        return $datas;
    }

    protected function getCurrentContextualDir()
    {
        return __DIR__;
    }

    /**
     * @param $configFile
     * @return ExtractCommand
     */
    private function launchExtractCommandWithMocckedLogger(
        $configFile
    ) {
        $command = new ExtractCommand($this->getLoggerMock());
        $this->commandTester = new CommandTester(
            $command
        );
        $this->commandTester->execute(
            [
                ExtractCommand::YAML_CONTEXT_FILE_ARGUMENT => $configFile,
            ]
        );

        return $command;
    }

    /**
     * @param $configFile
     * @return ExtractCommand
     */
    private function launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
        $configFile
    ) {
        // between each test we reset the admin error flag
        // because otherwise the admin error email will no longer be sent between each test
        $this->launchResetFlagAdminErrorCommand();

        $command = new ExtractCommand($this->getRealLogger());
        $this->commandTester = new CommandTester(
            $command
        );
        $this->commandTester->execute(
            [
                ExtractCommand::YAML_CONTEXT_FILE_ARGUMENT => $configFile,
            ]
        );

        return $command;
    }

    /**
     * @param $configFile
     * @return ExtractCommand
     */
    private function launchExtractCommandWithRealLoggerWithoutFlagAdminErrorReseted(
        $configFile
    ) {
        $command = new ExtractCommand($this->getRealLogger());
        $this->commandTester = new CommandTester(
            $command
        );
        $this->commandTester->execute(
            [
                ExtractCommand::YAML_CONTEXT_FILE_ARGUMENT => $configFile,
            ]
        );

        return $command;
    }

    private function launchResetFlagAdminErrorCommand()
    {
        $command = new ResetFlagAdminErrorOccured($this->getRealLogger());
        $this->commandTester = new CommandTester(
            $command
        );
        $this->commandTester->execute([]);

    }

    /**
     * @param $configFile
     * @param $classError
     * @param $errorTarget
     * @param $contextualFileForTest
     */
    private function assertThatEmailWasSentAndErrorWasThrown(
        $configFile,
        $classError,
        $errorTarget,
        $contextualFileForTest
    )
    {
        $this->copyContextualTestFileWaitingToDistantWaitingFolder($contextualFileForTest);
        $command = $this->launchExtractCommandWithRealLoggerAfterFlagAdminErrorReset(
            $this->getTestConfigFileFolderPath() . $configFile,
        );
        $this->assertThatErrorHasThrownAndEmailSent(
            $classError,
            $errorTarget,
            $command
        );
    }
}