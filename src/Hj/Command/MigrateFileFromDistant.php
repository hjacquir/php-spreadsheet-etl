<?php
/**
 * User: h.jacquir
 * Date: 21/01/2020
 * Time: 13:42
 */

namespace Hj\Command;

use Buuum\Ftp\Connection;
use Buuum\Ftp\FtpWrapper;
use Hj\Adapter\HtmlFormatterAdapter;
use Hj\Collector\CollectorIterator;
use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\DirectoryNotExistError;
use Hj\Error\FtpFailureConnexion;
use Hj\Error\FtpFailureDownloadFile;
use Hj\Factory\MailHandlerFactory;
use Hj\Helper\CatchedErrorHandler;
use Hj\Notifier\MailNotifier;
use Hj\Processor\FileProcessor;
use Hj\Strategy\File\MigrateFileStrategy;
use Hj\Strategy\Notifier\NotifyAdminStrategyOnSuccesfull;
use Hj\Strategy\Notifier\NotifyAdminStrategyWhenErrorOccured;
use Hj\Validator\ConfigFileValidator;
use Hj\YamlConfigLoader;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateFileFromDistant
 * @package Hj\Command
 */
class MigrateFileFromDistant extends AbstractCommand
{
    const COMMAND_NAME = 'spreadsheet-etl:migrate';
    const YAML_CONTEXT_FILE_ARGUMENT = 'yamlContextFilePath';
    const YAML_CONTEXT_FILE_ARGUMENT_DESCRIPTION = 'The path to the file configuration file in Yaml';

    /**
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
        $this->setDescription("this command allows you to migrate the files deposited on the remote FTP server.");
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
        $this->logger->debug("Starting file migration from the ftp server ...");

        $yamlConfigFilePath = $input->getArgument(self::YAML_CONTEXT_FILE_ARGUMENT);
        $configLoader = new YamlConfigLoader(
            $yamlConfigFilePath,
            new ConfigFileValidator(
                $yamlConfigFilePath
            )
        );
        $ftpHost = $configLoader->getFtpHost();
        $ftpUsername = $configLoader->getFtpUsername();
        $ftpPassword = $configLoader->getFtpPassword();
        $ftpPort = $configLoader->getFtpPort();

        $this->errorCollector = new ErrorCollector(
            new CollectorIterator()
        );
        $connection = new Connection(
            $ftpHost,
            $ftpUsername,
            $ftpPassword,
            $ftpPort,
            90,
            true
        );
        $migrationMessageOnSuccesfull = '';
        try {
            $connection->open();
            $ftp = new FtpWrapper($connection);
            $waitingDirectory = new WaitingDirectory(
                new BaseDirectory(
                    $configLoader->getWaitingFilePath(),
                    new CatchedErrorHandler(
                        $this->errorCollector
                    ),
                    new DirectoryNotExistError()
                )
            );
            $migrationStrategy = new MigrateFileStrategy(
                $waitingDirectory,
                $ftp,
                $configLoader,
                $this->logger,
                $this->errorCollector,
                new FtpFailureDownloadFile()
            );
            $processor = new FileProcessor(
                $this->logger,
                [
                    $migrationStrategy,
                ]
            );
            $processor->process();
            $migrationMessageOnSuccesfull = $migrationStrategy->getMigrationMessage();
        } catch (\Exception $e) {
            $this->errorCollector->addError(
                new FtpFailureConnexion($e)
            );
            echo $e->getMessage();
        }

        $swiftMessage = new Swift_Message();

        $handlerFactory = new MailHandlerFactory(
            new Swift_Mailer(
                new Swift_SmtpTransport(
                    $configLoader->getSmtpHost()
                )
            )
        );

        $notifyStrategies = [
            new NotifyAdminStrategyWhenErrorOccured(
                $this->errorCollector,
                "Spreadsheet-etl had encountered the belows errors when migrating file from the server : \n\n"
            ),
            new NotifyAdminStrategyOnSuccesfull(
                $this->errorCollector,
                'Spreadsheet-etl : file migration from FTP server',
                $migrationMessageOnSuccesfull
            )
        ];

        $mailNotifier = new MailNotifier(
            new HtmlFormatterAdapter(
                "d/m/Y"
            ),
            $notifyStrategies,
            $swiftMessage,
            $configLoader,
            $handlerFactory,
            $this->logger,
            []
        );

        $mailNotifier->notify();

        $this->logger->debug("End of file migration from ftp server.");
        return 0;
    }

    /**
     * @return ErrorCollector
     */
    public function getErrorCollector()
    {
        return $this->errorCollector;
    }
}