<?php
/**
 * User: h.jacquir
 * Date: 15/01/2020
 * Time: 17:05
 */

namespace Hj\Command;

use Hj\Adapter\HtmlFormatterAdapter;
use Hj\Cloner\CellAdapterCloner;
use Hj\Cloner\PersonCloner;
use Hj\Cloner\RowAdapterCloner;
use Hj\Collector\CollectorIterator;
use Hj\Collector\ErrorCollector;
use Hj\Collector\RowCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\ConfigFileMismatchError;
use Hj\Error\Data\DataDateInvalidError;
use Hj\Error\Data\DataLengthReachedError;
use Hj\Error\Data\DataMandatoryMissingError;
use Hj\Error\Database\DatabaseConnexionError;
use Hj\Error\Database\DoctrinePersistenceError;
use Hj\Error\DuplicateHeaderError;
use Hj\Error\File\DirectoryNotExistError;
use Hj\Error\File\FileWithoutExtensionError;
use Hj\Error\File\GettingSheetFromFileError;
use Hj\Error\File\LoadingFileError;
use Hj\Error\File\OnSettingValueError;
use Hj\Error\FileExtensionError;
use Hj\Error\FileNotFoundToConvertError;
use Hj\Error\FileWithMultipleSheetsError;
use Hj\Error\HeaderNotOnFirstRowError;
use Hj\Error\MandatoryHeaderMissing;
use Hj\Extractor;
use Hj\Factory\DatabaseConfigFactory;
use Hj\Factory\FileHeaderConfigFactory;
use Hj\Factory\FilePathConfigFactory;
use Hj\Factory\MailConfigFactory;
use Hj\Factory\MailHandlerFactory;
use Hj\Factory\SmtpConfigFactory;
use Hj\File\CellAdapter;
use Hj\File\Field\BirthDate;
use Hj\File\Field\FirstName;
use Hj\File\Field\LastName;
use Hj\File\RowAdapter;
use Hj\FileManipulator;
use Hj\Helper\CatchedErrorHandler;
use Hj\Model\Person;
use Hj\Normalizer\AccentsRemoverNormalizer;
use Hj\Normalizer\DateStringExcelNormalizer;
use Hj\Normalizer\RemoveSpaceNormalizer;
use Hj\Normalizer\ToUpperNormalizer;
use Hj\Normalizer\TrimNormalizer;
use Hj\Notifier\MailNotifier;
use Hj\Parser\CsvParser;
use Hj\Parser\OdsParser;
use Hj\Parser\XlsParser;
use Hj\Parser\XlsxParser;
use Hj\Processor\FileProcessor;
use Hj\Strategy\Admin\GenerateFlagNotificationAlreadySendErrorOccured;
use Hj\Strategy\Admin\NotificationAlreadySendOnError;
use Hj\Strategy\Data\CheckFieldConfigStrategy;
use Hj\Strategy\Data\CollectRowAdapterStrategy;
use Hj\Strategy\Data\DataValidationStrategy;
use Hj\Strategy\Data\RowsExtractionStrategy;
use Hj\Strategy\Database\InitializeEntityManagerStrategy;
use Hj\Strategy\Database\SaveDatasOnDatabase;
use Hj\Strategy\File\Archive;
use Hj\Strategy\File\CheckFileExtension;
use Hj\Strategy\File\CheckIfFileHasMultipleSheet;
use Hj\Strategy\File\CheckThatFileHasExtension;
use Hj\Strategy\File\CollectInitialFileNameFromWaitingDirectory;
use Hj\Strategy\File\CopyFromWaitingToInProcessing;
use Hj\Strategy\File\CopyToFailureDirectory;
use Hj\Strategy\File\CsvFileEncodingConverter;
use Hj\Strategy\File\DeleteFromWaiting;
use Hj\Strategy\File\FileForExtractionExist;
use Hj\Strategy\File\ResetProcessingFolder;
use Hj\Strategy\Header\HeaderExtraction;
use Hj\Strategy\Header\HeaderUnicityChecker;
use Hj\Strategy\Header\MandatoryHeadersChecker;
use Hj\Strategy\Header\OnFirstRowHeaderChecker;
use Hj\Strategy\Notifier\NotifyAdminStrategyWhenErrorOccured;
use Hj\Strategy\Notifier\NotifyAdminStrategyWhenErrorOccuredOnlyOnce;
use Hj\Strategy\Notifier\NotifyUserStrategyOnSuccesfull;
use Hj\Strategy\Notifier\NotifyUserStrategyWhenErrorOccured;
use Hj\Strategy\Strategy;
use Hj\ValidationByPasser;
use Hj\Validator\ConfigFileValidator;
use Hj\Validator\Data\DataDateFormatValidator;
use Hj\Validator\Data\DataLengthReachedValidator;
use Hj\Validator\Data\DataMandatoryValidator;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExtractCommand
 * @package Hj\Command
 */
class ExtractCommand extends AbstractCommand
{
    const COMMAND_NAME = 'spreadsheet-etl:extract';
    const YAML_CONTEXT_FILE_ARGUMENT = 'yamlContextFilePath';
    const YAML_CONTEXT_FILE_ARGUMENT_DESCRIPTION = 'The path to the file configuration file in Yaml';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Represent all strategies added to the processor
     *
     * @var Strategy[]
     */
    private $addedStrategies = [];

    /**
     * Represent all instantiated strategies
     *
     * @var Strategy[]
     */
    private $instantiatedStrategies = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * ExtractCommand constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription("this command extracts the data from the file and saves it in the database.");
        $this
            ->addArgument(
                self::YAML_CONTEXT_FILE_ARGUMENT,
                InputArgument::REQUIRED,
                self::YAML_CONTEXT_FILE_ARGUMENT_DESCRIPTION
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->debug("Extraction started ...");

        $yamlConfigFilePath = $input->getArgument(self::YAML_CONTEXT_FILE_ARGUMENT);

        $filePathConfig = (new FilePathConfigFactory())->createConfig($yamlConfigFilePath);

        $waitingDir = $filePathConfig->getWaitingDir()->getValue();
        $inProcessingDir = $filePathConfig->getInProcessing()->getValue();
        $archivedDir = $filePathConfig->getArchived()->getValue();
        $failureFilePath = $filePathConfig->getFailure()->getValue();

        $fileManipulator = new FileManipulator();
        $this->errorCollector = new ErrorCollector(new CollectorIterator());

        $catchedErrorHandler = new CatchedErrorHandler(
            $this->errorCollector
        );

        $waitingDirectory = new WaitingDirectory(
            new BaseDirectory(
                $waitingDir,
                $catchedErrorHandler,
                new DirectoryNotExistError()
            )
        );
        $inProcessingDirectory = new BaseDirectory(
            $inProcessingDir,
            $catchedErrorHandler,
            new DirectoryNotExistError()
        );
        $archivedDirectory = new BaseDirectory(
            $archivedDir,
            $catchedErrorHandler,
            new DirectoryNotExistError()
        );
        $failureDirectory = new BaseDirectory(
            $failureFilePath,
            $catchedErrorHandler,
            new DirectoryNotExistError()
        );

        $accentsRemoverNormalizer = new AccentsRemoverNormalizer();
        $toUpperNormalizer = new ToUpperNormalizer();
        $trimNormalizer = new TrimNormalizer();

        $extractor = new Extractor(
            $waitingDirectory,
            $catchedErrorHandler,
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $inProcessingDirectory,
            );

        $csvParser = new CsvParser(
            $waitingDirectory,
            new OnSettingValueError(),
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $catchedErrorHandler,
            $inProcessingDirectory,
            $extractor,
            $accentsRemoverNormalizer,
            $toUpperNormalizer,
            $trimNormalizer
        );
        $odsParser = new OdsParser(
            $waitingDirectory,
            new OnSettingValueError(),
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $catchedErrorHandler,
            $inProcessingDirectory,
            $extractor,
            $accentsRemoverNormalizer,
            $toUpperNormalizer,
            $trimNormalizer
        );
        $xlsParser = new XlsParser(
            $waitingDirectory,
            new OnSettingValueError(),
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $catchedErrorHandler,
            $inProcessingDirectory,
            $extractor,
            $accentsRemoverNormalizer,
            $toUpperNormalizer,
            $trimNormalizer
        );
        $xlsxParser = new XlsxParser(
            $waitingDirectory,
            new OnSettingValueError(),
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $catchedErrorHandler,
            $inProcessingDirectory,
            $extractor,
            $accentsRemoverNormalizer,
            $toUpperNormalizer,
            $trimNormalizer
        );

        $parsers = [
            $csvParser,
            $odsParser,
            $xlsParser,
            $xlsxParser,
        ];

        $removeSpaceNormalizer = new RemoveSpaceNormalizer();

        $lastName = new LastName();
        $firstName = new FirstName();
        $birthDate = new BirthDate();

        $fieldsToExtract = [
            $lastName,
            $firstName,
            $birthDate,
        ];
        $fileHeadersConfig = (new FileHeaderConfigFactory())->createConfig($yamlConfigFilePath);

        // check if fields in list above are already
        // defined into the .yaml config file
        $checkFieldConfigStrategy = new CheckFieldConfigStrategy(
            $fileHeadersConfig,
            $fieldsToExtract,
            $this->errorCollector,
            new ConfigFileMismatchError()
        );

        // check if files are available for extraction
        $fileForExtractionExist = new FileForExtractionExist(
            $waitingDirectory,
            $this->logger
        );
        $databaseConfigs = (new DatabaseConfigFactory())->createConfig($yamlConfigFilePath);
        // initialization of the entity manager for database queries
        $initializeEntityManagerStrategy = new InitializeEntityManagerStrategy(
            $databaseConfigs,
            $catchedErrorHandler,
            __DIR__ . "/../../../doctrine",
            __DIR__ . "/../../../doctrineProxies",
            true,
            new DatabaseConnexionError(),
            $waitingDirectory
        );

        // we delete and recreate the directory in processing in order to have only one file in processing
        $resetProcessingStrategy = new ResetProcessingFolder(
            $inProcessingDirectory
        );

        // we save the name of the initial file before moving it
        $collectInitialFileName = new CollectInitialFileNameFromWaitingDirectory(
            $this->errorCollector,
            $waitingDirectory,
        );

        // we check if file has an extension
        $checkIfFileIsWithoutExtension = new CheckThatFileHasExtension(
          $waitingDirectory,
          $this->errorCollector,
          new FileWithoutExtensionError()
        );

        // we move the first file while waiting in the directory in progressing in order to process it
        $moveFromWaitingToProcessing = new CopyFromWaitingToInProcessing(
            $this->errorCollector,
            $waitingDirectory,
            $inProcessingDirectory,
            $fileManipulator
        );

        // we check if the format is supported
        $checkFileExtension = new CheckFileExtension(
            $inProcessingDirectory,
            $this->errorCollector,
            new FileExtensionError()
        );

        // if the file is a CSV we convert it to UTF-8 to avoid the wrong encoding of accents
        $convertCsvToUtf8 = new CsvFileEncodingConverter(
            $csvParser,
            $inProcessingDirectory,
            $this->errorCollector,
            new FileNotFoundToConvertError()
        );

        // we check if the file contains multiple sheets
        $checkIfFileHasMultipleSheets = new CheckIfFileHasMultipleSheet(
            $inProcessingDirectory,
            $parsers,
            $this->errorCollector,
            new FileWithMultipleSheetsError()
        );

        // we check that the header is on the first line
        $checkIfHeaderIsOnFirstRow = new OnFirstRowHeaderChecker(
            $inProcessingDirectory,
            $parsers,
            $this->errorCollector,
            new HeaderNotOnFirstRowError()
        );

        // we extract the header if everything is OK
        $extractHeaderStrategy = new HeaderExtraction(
            $this->errorCollector,
            $inProcessingDirectory,
            $parsers
        );

        // check that mandatory headers are filled
        $mandatoryHeadersChecker = new MandatoryHeadersChecker(
            $fileHeadersConfig,
            $inProcessingDirectory,
            $this->errorCollector,
            new MandatoryHeaderMissing(),
            $extractHeaderStrategy
        );

        // check that the header columns are not duplicated
        $checkUnicityHeader = new HeaderUnicityChecker(
            $inProcessingDirectory,
            $extractHeaderStrategy,
            $this->errorCollector,
            new DuplicateHeaderError()
        );

        // extract all rows
        $rowsExtractionStrategy = new RowsExtractionStrategy(
            $this->errorCollector,
            $inProcessingDirectory,
            $parsers
        );

        // collect rows with their associated cells with the appropriate header
        $collectRowAdapterStrategy = new CollectRowAdapterStrategy(
            $this->errorCollector,
            new DateStringExcelNormalizer($birthDate),
            $inProcessingDirectory,
            new RowCollector(new CollectorIterator()),
            new RowAdapterCloner(new RowAdapter()),
            new CellAdapterCloner(new CellAdapter()),
            $rowsExtractionStrategy,
            $extractHeaderStrategy,
            $trimNormalizer,
            $accentsRemoverNormalizer,
            $toUpperNormalizer
        );

        // validate datas
        $validateDatas = new DataValidationStrategy(
            $this->errorCollector,
            $collectRowAdapterStrategy,
            $fieldsToExtract,
            $inProcessingDirectory
        );

        $saveDataToDatabase = new SaveDatasOnDatabase(
            new PersonCloner(new Person()),
            $firstName,
            $lastName,
            $birthDate,
            $collectRowAdapterStrategy,
            $catchedErrorHandler,
            new DoctrinePersistenceError(),
            $initializeEntityManagerStrategy,
            $inProcessingDirectory
        );

        // if an error occurs we move the file to the
        // failure directory
        $copyToFailureDirectory = new CopyToFailureDirectory(
            $this->errorCollector,
            $failureDirectory,
            $fileManipulator,
            $inProcessingDirectory
        );

        // if everything went well we archive the original file
        $archiveStrategy = new Archive(
            $this->errorCollector,
            $archivedDirectory,
            $fileManipulator,
            $inProcessingDirectory
        );

        // at the end of the process we delete the uploaded file
        $deleteFileWaiting = new DeleteFromWaiting(
            $this->errorCollector,
            $waitingDirectory,
            $inProcessingDirectory
        );

        // we must check if an admin error has already been encountered
        // and if so do not send the notification N times in the cycle
        $checkIfAdminNotificationAlreadySend = new NotificationAlreadySendOnError();
        // generation of the admin error flag if an error was encountered
        $generateFlagAdminWhenAdminErrorOccured = new GenerateFlagNotificationAlreadySendErrorOccured(
            $checkIfAdminNotificationAlreadySend,
            $this->errorCollector
        );

        $this->addedStrategies = [
            $checkFieldConfigStrategy,
            $fileForExtractionExist,
            $initializeEntityManagerStrategy,
            $resetProcessingStrategy,
            $collectInitialFileName,
            $checkIfFileIsWithoutExtension,
            $moveFromWaitingToProcessing,
            $checkFileExtension,
            $convertCsvToUtf8,
            $checkIfFileHasMultipleSheets,
            $checkIfHeaderIsOnFirstRow,
            $extractHeaderStrategy,
            $mandatoryHeadersChecker,
            $checkUnicityHeader,
            $rowsExtractionStrategy,
            $collectRowAdapterStrategy,
            $validateDatas,
            $saveDataToDatabase,
            $copyToFailureDirectory,
            $archiveStrategy,
            $deleteFileWaiting,
            $checkIfAdminNotificationAlreadySend,
            $generateFlagAdminWhenAdminErrorOccured
        ];

        // only for functional tests to check if all instantiated strategies are added
        $vars = get_defined_vars();
        $this->instantiatedStrategies = $this->getAllInstance(Strategy::class, $vars);

        // adding validators
        $byPassedValues = [
            null,
            "",
        ];

        $validationByPasser = new ValidationByPasser($byPassedValues);
        $firstName->addValidator(
            new DataMandatoryValidator(
                $this->errorCollector,
                new DataMandatoryMissingError()
            )
        );
        $firstName->addValidator(
            new DataLengthReachedValidator(
                FirstName::MAXIMAL_LENGTH,
                $this->errorCollector,
                new DataLengthReachedError(
                    FirstName::MAXIMAL_LENGTH
                ),
                $removeSpaceNormalizer
            )
        );
        $lastName->addValidator(
            new DataMandatoryValidator(
                $this->errorCollector,
                new DataMandatoryMissingError()
            )
        );
        $lastName->addValidator(
            new DataLengthReachedValidator(
                LastName::MAXIMAL_LENGTH,
                $this->errorCollector,
                new DataLengthReachedError(
                    LastName::MAXIMAL_LENGTH
                ),
                $removeSpaceNormalizer
            )
        );
        $birthDate->addValidator(
            new DataMandatoryValidator(
                $this->errorCollector,
                new DataMandatoryMissingError()
            )
        );
        $dateFormatValidator = new DataDateFormatValidator(
            $this->errorCollector,
            new DataDateInvalidError(),
            $removeSpaceNormalizer,
            $validationByPasser
        );
        $birthDate->addValidator(
            $dateFormatValidator
        );

        // process the file
        $fileProcessor = new FileProcessor(
            $this->logger,
            $this->addedStrategies
        );
        $fileProcessor->process();

        $swiftMessage = new Swift_Message();

        $smtpConfigs = (new SmtpConfigFactory())->createConfig($yamlConfigFilePath);

        $mailer = new Swift_Mailer(
            new Swift_SmtpTransport(
                $smtpConfigs->getHost()->getValue()
            )
        );

        $handlerFactory = new MailHandlerFactory($mailer);

        $mailsConfig = (new MailConfigFactory())->createConfig($yamlConfigFilePath);

        $notifyStrategies = [
            new NotifyUserStrategyWhenErrorOccured(
                $mailsConfig,
                $copyToFailureDirectory,
                $this->errorCollector
            ),
            new NotifyAdminStrategyWhenErrorOccuredOnlyOnce(
                $checkIfAdminNotificationAlreadySend,
                new NotifyAdminStrategyWhenErrorOccured(
                    $mailsConfig,
                    $this->errorCollector,
                    "Spreadsheet-etl had encountered the belows errors : " . "\n\n"
                ),
                ),
            new NotifyUserStrategyOnSuccesfull(
                $mailsConfig,
                $saveDataToDatabase,
                $archiveStrategy,
                $this->errorCollector
            ),
        ];

        $mailNotifier = new MailNotifier(
            $mailsConfig,
            new HtmlFormatterAdapter(
                "d/m/Y H:i:s"
            ),
            $notifyStrategies,
            $swiftMessage,
            $handlerFactory,
            $this->logger,
            [
            ]
        );

        $mailNotifier->notify();

        $this->logger->debug("Extraction operation finished.");

        return 0;
    }

    /**
     * @return Strategy[]
     */
    public function getAddedStrategies(): array
    {
        return $this->addedStrategies;
    }

    /**
     * @return Strategy[]
     */
    public function getInstantiatedStrategies(): array
    {
        return $this->instantiatedStrategies;
    }

    /**
     * @return ErrorCollector
     */
    public function getErrorCollector(): ErrorCollector
    {
        return $this->errorCollector;
    }

    /**
     * @return HeaderExtraction
     */
    public function getHeaderExtractionStrategy()
    {
        return $this->getSpecificAddedStrategy(HeaderExtraction::class);
    }

    /**
     * @return CollectRowAdapterStrategy
     */
    public function getRowAdapterCollectorStrategy()
    {
        return $this->getSpecificAddedStrategy(CollectRowAdapterStrategy::class);
    }

    /**
     * @param string $class The searched class
     * @param array $vars An array of defined vars on the script
     *
     * @return array
     */
    private function getAllInstance($class, $vars)
    {
        $values = [];

        foreach ($vars as $key => $value) {
            if ($value instanceof $class) {
                array_push($values, $value);
            }
        }

        return $values;
    }

    /**
     * @param $className
     * @return Strategy
     */
    private function getSpecificAddedStrategy($className)
    {
        foreach ($this->addedStrategies as $addedStrategy) {
            if ($addedStrategy instanceof $className) {
                return $addedStrategy;
            }
        }

        return null;
    }
}