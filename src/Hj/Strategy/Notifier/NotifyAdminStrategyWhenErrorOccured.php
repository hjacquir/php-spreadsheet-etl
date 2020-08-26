<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:10
 */

namespace Hj\Strategy\Notifier;

use Hj\Collector\ErrorCollector;
use Hj\Config\MailsConfig;

/**
 * Class NotifyAdminStrategyWhenErrorOccured
 * @package Hj\Strategy\Notifier
 */
class NotifyAdminStrategyWhenErrorOccured implements NotifierStrategy
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * Message on the header
     *
     * @var string
     */
    private $bodyMessage;

    /**
     * @var MailsConfig
     */
    private $mailsConfig;

    /**
     * NotifyAdminStrategyWhenErrorOccured constructor.
     * @param MailsConfig $mailsConfig
     * @param ErrorCollector $errorCollector
     * @param string $bodyMessage
     */
    public function __construct(
        MailsConfig $mailsConfig,
        ErrorCollector $errorCollector,
        $bodyMessage
    )
    {
        $this->mailsConfig = $mailsConfig;
        $this->errorCollector = $errorCollector;
        $this->bodyMessage = $bodyMessage;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->errorCollector->hasErrorForAdmins();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errorCollector->getAllAdminErrors();
    }

    /**
     * @return array
     * @throws \Hj\Exception\KeyNotExist
     */
    public function getSendTo()
    {
        return $this->mailsConfig
            ->getAdmins()
            ->getValue();
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        return $this->bodyMessage;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return "Spreadsheet-etl : critical error";
    }
}