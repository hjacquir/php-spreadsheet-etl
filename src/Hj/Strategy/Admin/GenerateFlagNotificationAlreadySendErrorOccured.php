<?php
/**
 * User: h.jacquir
 * Date: 03/06/2020
 * Time: 16:05
 */

namespace Hj\Strategy\Admin;

use Hj\Collector\ErrorCollector;
use Hj\Strategy\Strategy;

/**
 * Class GenerateFlagNotificationAlreadySendErrorOccured
 *
 * @package Hj\Strategy\Admin
 */
class GenerateFlagNotificationAlreadySendErrorOccured implements Strategy
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var NotificationAlreadySendOnError
     */
    private $notificationAlreadySendStrategy;

    /**
     * GenerateFlagNotificationAlreadySendErrorOccured constructor.
     * @param NotificationAlreadySendOnError $notificationAlreadySendStrategy
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        NotificationAlreadySendOnError $notificationAlreadySendStrategy,
        ErrorCollector $errorCollector
    )
    {
        $this->notificationAlreadySendStrategy = $notificationAlreadySendStrategy;
        $this->errorCollector = $errorCollector;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->errorCollector->hasErrorForAdmins() &&
            false === $this->notificationAlreadySendStrategy->isNotificationIsAlreadySend();
    }

    public function apply()
    {
        touch($this->notificationAlreadySendStrategy->getFileFlagPath());
        file_put_contents(
            $this->notificationAlreadySendStrategy->getFileFlagPath(),
            "Speadsheet-etl encountered an critical error for administrator." .
            " An email had been sent to the administrator." .
            " This file was generated in order not to send the notification email to the administrator N times."
        );
    }
}