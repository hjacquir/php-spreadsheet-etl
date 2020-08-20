<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:53
 */

namespace Hj\Strategy\Notifier;

use Hj\Collector\ErrorCollector;
use Hj\Config\MailsConfig;
use Hj\Strategy\File\CopyToFailureDirectory;
use Hj\YamlConfigLoader;

/**
 * Class NotifyUserStrategyWhenErrorOccured
 * @package Hj\Strategy\Notifier
 */
class NotifyUserStrategyWhenErrorOccured implements NotifierStrategy
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var CopyToFailureDirectory
     */
    private $copyToFailureFolderStrategy;

    /**
     * @var MailsConfig
     */
    private $mailsConfig;

    /**
     * NotifyUserStrategyWhenErrorOccured constructor.
     * @param MailsConfig $mailsConfig
     * @param CopyToFailureDirectory $copyToFailureFolderStrategy
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        MailsConfig $mailsConfig,
        CopyToFailureDirectory $copyToFailureFolderStrategy,
        ErrorCollector $errorCollector
    ) {
        $this->mailsConfig = $mailsConfig;
        $this->errorCollector = $errorCollector;
        $this->copyToFailureFolderStrategy = $copyToFailureFolderStrategy;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        // notify user only if error admin not exist because admin error are priority
        return $this->errorCollector->hasErrorForUsers()
            && false === $this->errorCollector->hasErrorForAdmins();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errorCollector->getAllUserErrors();
    }

    /**
     * @param YamlConfigLoader $configLoader
     * @return array
     * @throws \Hj\Exception\KeyNotExist
     */
    public function getSendTo(YamlConfigLoader $configLoader)
    {
        return $this->mailsConfig->getUsers()->getValue();
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        $body = "Spreadsheet-etl had encountered the belows errors " .
            "on the file : \n" .
            $this->copyToFailureFolderStrategy->getDestination() .
            "\n\n";

        return $body;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return "Spreadsheet-etl : critical error";
    }
}