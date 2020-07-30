<?php
/**
 * User: h.jacquir
 * Date: 03/06/2020
 * Time: 16:05
 */

namespace Hj\Strategy\Admin;

use Hj\Strategy\AbstractNotificationAlreadySendStrategy;

/**
 * Class NotificationAlreadySendOnError
 *
 * @package Hj\Strategy\Admin
 */
class NotificationAlreadySendOnError extends AbstractNotificationAlreadySendStrategy
{
    /**
     * @return string
     */
    public function getFileFlagPath()
    {
        return __DIR__ . "/../../../../localWorkspace/admin_error_occured.txt";
    }
}