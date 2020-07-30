<?php
/**
 * User: h.jacquir
 * Date: 05/06/2020
 * Time: 12:57
 */

namespace Hj\Strategy;

/**
 * Class AbstractNotificationAlreadySendStrategy
 * @package Hj\Strategy
 */
abstract class AbstractNotificationAlreadySendStrategy implements Strategy
{
    /**
     * @var bool
     */
    private $notificationIsAlreadySend = false;

    /**
     * @return string
     */
    public abstract function getFileFlagPath();

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return file_exists($this->getFileFlagPath());
    }

    public function apply()
    {
        $this->notificationIsAlreadySend = true;
    }

    /**
     * @return bool
     */
    public function isNotificationIsAlreadySend(): bool
    {
        return $this->notificationIsAlreadySend;
    }
}