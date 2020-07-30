<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 11:10
 */

namespace Hj\Strategy\Notifier;

use Hj\Strategy\Admin\NotificationAlreadySendOnError;
use Hj\YamlConfigLoader;

/**
 * Class NotifyAdminStrategyWhenErrorOccuredOnlyOnce
 * @package Hj\Strategy\Notifier
 */
class NotifyAdminStrategyWhenErrorOccuredOnlyOnce implements NotifierStrategy
{
    /**
     * @var NotifyAdminStrategyWhenErrorOccured
     */
    private $notifyAdminStrategyWhenErrorOccured;

    /**
     * @var NotificationAlreadySendOnError
     */
    private $checkIfAdminNotificationAlreadySend;

    /**
     * NotifyAdminStrategyWhenErrorOccured constructor.
     * @param NotificationAlreadySendOnError $checkIfAdminNotificationAlreadySend
     * @param NotifyAdminStrategyWhenErrorOccured $notifyAdminStrategyWhenErrorOccured
     */
    public function __construct(
        NotificationAlreadySendOnError $checkIfAdminNotificationAlreadySend,
        NotifyAdminStrategyWhenErrorOccured $notifyAdminStrategyWhenErrorOccured
    )
    {
        $this->checkIfAdminNotificationAlreadySend = $checkIfAdminNotificationAlreadySend;
        $this->notifyAdminStrategyWhenErrorOccured = $notifyAdminStrategyWhenErrorOccured;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->notifyAdminStrategyWhenErrorOccured->isAppropriate()
            && false === $this->checkIfAdminNotificationAlreadySend->isNotificationIsAlreadySend();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->notifyAdminStrategyWhenErrorOccured->getErrors();
    }

    /**
     * @param YamlConfigLoader $configLoader
     * @return array
     */
    public function getSendTo(YamlConfigLoader $configLoader)
    {
        return $this->notifyAdminStrategyWhenErrorOccured->getSendTo($configLoader);
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        return $this->notifyAdminStrategyWhenErrorOccured->getBodyMessage();
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->notifyAdminStrategyWhenErrorOccured->getSubject();
    }
}