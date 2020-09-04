<?php
/**
 * User: h.jacquir
 * Date: 21/01/2020
 * Time: 13:42
 */

namespace Hj\Command;

use Hj\Adapter\HtmlFormatterAdapter;
use Hj\Collector\CollectorIterator;
use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\DirectoryNotExistError;
use Hj\Factory\FilePathConfigFactory;
use Hj\Factory\MailConfigFactory;
use Hj\Factory\MailHandlerFactory;
use Hj\Factory\SmtpConfigFactory;
use Hj\Helper\CatchedErrorHandler;
use Hj\Notifier\MailNotifier;
use Hj\Strategy\Notifier\NotifyAllOnceWhenNoFileForExtraction;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckIfFileWaitingForExtractionExist
 * @package Hj\Command
 */
class CheckIfFileWaitingForExtractionExist extends AbstractCommand
{
    const COMMAND_NAME = 'spreadsheet-etl:check-file-waiting';
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
        $this->setDescription("This command is used to check whether files awaiting processing exist. If no file is available it sends a notification to the administrator.");
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
     * @return int
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->debug("Start of checking for the existence of files awaiting extraction ...");
        $yamlConfigFilePath = $input->getArgument(self::YAML_CONTEXT_FILE_ARGUMENT);

        $filePathConfig = (new FilePathConfigFactory())
            ->createConfig($yamlConfigFilePath);

        $waitingDir = $filePathConfig->getWaitingDir()->getValue();

        $catchedErrorHandler = new CatchedErrorHandler(
            new ErrorCollector(
                new CollectorIterator()
            )
        );

        $waitingDirectory = new WaitingDirectory(
            new BaseDirectory(
                $waitingDir,
                $catchedErrorHandler,
                new DirectoryNotExistError()
            )
        );

        $mailsConfig = (new MailConfigFactory())
            ->createConfig($yamlConfigFilePath);

        $smtpConfigs = (new SmtpConfigFactory())
            ->createConfig($yamlConfigFilePath);

        $mailNotifier = new MailNotifier(
            $mailsConfig,
            new HtmlFormatterAdapter(
                "d/m/Y H:i:s"
            ),
            [
                new NotifyAllOnceWhenNoFileForExtraction(
                    $mailsConfig,
                    $waitingDirectory
                ),
            ],
            new Swift_Message(),
            new MailHandlerFactory(
                new Swift_Mailer(
                    new Swift_SmtpTransport(
                        $smtpConfigs->getHost()->getValue()
                    )
                )
            ),
            $this->logger,
            []
        );

        $mailNotifier->notify();

        $this->logger->debug("End of checking the existence of files awaiting extraction.");
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