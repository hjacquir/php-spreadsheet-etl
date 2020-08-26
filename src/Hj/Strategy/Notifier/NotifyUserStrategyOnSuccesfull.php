<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:53
 */

namespace Hj\Strategy\Notifier;

use Hj\Collector\ErrorCollector;
use Hj\Config\MailsConfig;
use Hj\Strategy\Database\SaveDatasOnDatabase;
use Hj\Strategy\File\Archive;

/**
 * Class NotifyUserStrategyOnSuccesfull
 * @package Hj\Strategy\Notifier
 */
class NotifyUserStrategyOnSuccesfull implements NotifierStrategy
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var Archive
     */
    private $archiveStrategy;

    /**
     * @var SaveDatasOnDatabase
     */
    private $saveDatasOnDatabaseStrategy;

    /**
     * @var MailsConfig
     */
    private $mailsConfig;

    /**
     * NotifyUserStrategyWhenErrorOccured constructor.
     * @param MailsConfig $mailsConfig
     * @param SaveDatasOnDatabase $saveDatasOnDatabaseStrategy
     * @param Archive $archiveStrategy
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        MailsConfig $mailsConfig,
        SaveDatasOnDatabase $saveDatasOnDatabaseStrategy,
        Archive $archiveStrategy,
        ErrorCollector $errorCollector
    ) {
        $this->mailsConfig = $mailsConfig;
        $this->saveDatasOnDatabaseStrategy = $saveDatasOnDatabaseStrategy;
        $this->archiveStrategy = $archiveStrategy;
        $this->errorCollector = $errorCollector;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false === $this->errorCollector->hasError()
            && $this->saveDatasOnDatabaseStrategy->hasSavedDatas();
    }

    /**
     * @return array
     * @throws \Hj\Exception\KeyNotExist
     */
    public function getSendTo()
    {
        return $this->mailsConfig->getUsers()->getValue();
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        $body = "Spreadsheet-etl saved succesfully data from the file : " .
            "\n" .
            $this->archiveStrategy->getDestination() .
            "\n\n";

        return $body;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return "Spreadsheet-etl : datas saved succesfully";
    }
}