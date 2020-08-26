<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:53
 */

namespace Hj\Strategy\Notifier;

use Hj\Collector\ErrorCollector;
use Hj\Config\MailsConfig;

/**
 * Class NotifyAdminStrategyOnSuccesfull
 * @package Hj\Strategy\Notifier
 */
class NotifyAdminStrategyOnSuccesfull implements NotifierStrategy
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var string
     */
    private $messageSubject;

    /**
     * @var string
     */
    private $messageBody;

    /**
     * @var MailsConfig
     */
    private $mailsConfig;

    /**
     * NotifyAdminStrategyOnSuccesfull constructor.
     * @param MailsConfig $mailsConfig
     * @param ErrorCollector $errorCollector
     * @param string $messageSubject
     * @param string $messageBody
     */
    public function __construct(
        MailsConfig $mailsConfig,
        ErrorCollector $errorCollector,
        string $messageSubject,
        string $messageBody
    )
    {
        $this->mailsConfig = $mailsConfig;
        $this->errorCollector = $errorCollector;
        $this->messageSubject = $messageSubject;
        $this->messageBody = $messageBody;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false === $this->errorCollector->hasError();
    }

    /**
     * @return array
     * @throws \Hj\Exception\KeyNotExist
     */
    public function getSendTo()
    {
        return $this->mailsConfig
            ->getUsers()
            ->getValue();
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        return $this->messageBody;
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
        return $this->messageSubject;
    }
}