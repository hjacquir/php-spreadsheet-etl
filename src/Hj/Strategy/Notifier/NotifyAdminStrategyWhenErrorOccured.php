<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:10
 */

namespace Hj\Strategy\Notifier;

use Hj\Collector\ErrorCollector;
use Hj\YamlConfigLoader;

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
     * NotifyAdminStrategyWhenErrorOccured constructor.
     * @param ErrorCollector $errorCollector
     * @param string $bodyMessage
     */
    public function __construct(
        ErrorCollector $errorCollector,
        $bodyMessage
    )
    {
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
     * @param YamlConfigLoader $configLoader
     * @return array
     */
    public function getSendTo(YamlConfigLoader $configLoader)
    {
        return $configLoader->getAdminsMails();
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