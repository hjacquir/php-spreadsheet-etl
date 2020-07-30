<?php
/**
 * User: h.jacquir
 * Date: 23/01/2020
 * Time: 15:19
 */

namespace Hj\Factory;

use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;

/**
 * Class MailHandlerFactory
 * @package Hj\Factory
 */
class MailHandlerFactory
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * MailHandlerFactory constructor.
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function createMailHandler(Swift_Message $swiftMessage)
    {
        return new SwiftMailerHandler(
            $this->mailer,
            $swiftMessage,
            Logger::CRITICAL,
            false
        );
    }
}