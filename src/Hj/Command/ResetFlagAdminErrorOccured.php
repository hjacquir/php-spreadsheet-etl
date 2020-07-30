<?php
/**
 * User: h.jacquir
 * Date: 21/01/2020
 * Time: 13:42
 */

namespace Hj\Command;

use Hj\Collector\ErrorCollector;
use Hj\Strategy\Admin\NotificationAlreadySendOnError;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetFlagAdminErrorOccured
 * @package Hj\Command
 * @todo add functional tests
 */
class ResetFlagAdminErrorOccured extends AbstractCommand
{
    const COMMAND_NAME = 'spreadsheet-etl:reset-admin-error-flag';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

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
        $this->setDescription("this command removes the flag added when a critical administrator error has occurred.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->debug("Start of the deletion of the admin error flag ...");
        $notificationAlreadySendOnError = new NotificationAlreadySendOnError();
        $fileName = $notificationAlreadySendOnError->getFileFlagPath();
        $message = "No admin error flag file detected.";

        if (file_exists($fileName)) {
            unlink($fileName);
            $message = "The flag file for the admin error : {$fileName} had been removed succesfully.";
        }

        $this->logger->debug($message);


        $this->logger->debug("Removal of the flag of the admin error finished.");
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