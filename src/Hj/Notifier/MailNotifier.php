<?php
/**
 * User: h.jacquir
 * Date: 23/01/2020
 * Time: 13:35
 */

namespace Hj\Notifier;

use Hj\Error\Error;
use Hj\Factory\MailHandlerFactory;
use Hj\Strategy\Notifier\NotifierStrategy;
use Hj\Strategy\Strategy;
use Hj\YamlConfigLoader;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use Swift_Message;

/**
 * Class MailNotifier
 * @package Hj\Notifier
 */
class MailNotifier implements Notifier
{
    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var NotifierStrategy[]
     */
    private $mailNotifierStrategies;

    /**
     * @var Swift_Message
     */
    private $swiftMessage;

    /**
     * @var YamlConfigLoader
     */
    private $configLoader;

    /**
     * @var MailHandlerFactory
     */
    private $factory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Error[]
     */
    private $errors;

    /**
     * @var string
     */
    private $sendTo;

    /**
     * @var Strategy[}
     */
    private $attachmentStrategies = [];

    /**
     * MailNotifier constructor.
     * @param FormatterInterface $formatter
     * @param array $mailNotifierStrategies
     * @param Swift_Message $swiftMessage
     * @param YamlConfigLoader $configLoader
     * @param MailHandlerFactory $factory
     * @param Logger $logger
     * @param array $addAttachmentStrategies
     */
    public function __construct(
        FormatterInterface $formatter,
        array $mailNotifierStrategies,
        Swift_Message $swiftMessage,
        YamlConfigLoader $configLoader,
        MailHandlerFactory $factory,
        Logger $logger,
        array $addAttachmentStrategies
    ) {
        $this->formatter = $formatter;
        $this->mailNotifierStrategies = $mailNotifierStrategies;
        $this->swiftMessage = $swiftMessage;
        $this->configLoader = $configLoader;
        $this->factory = $factory;
        $this->logger = $logger;
        $this->attachmentStrategies = $addAttachmentStrategies;
    }

    public function notify()
    {
        $this->swiftMessage->setFrom([$this->configLoader->getMailFrom()]);

        foreach ($this->attachmentStrategies as $attachmentStrategy) {
            $attachmentStrategy->apply();
        }

        foreach ($this->mailNotifierStrategies as $mailNotifierStrategy) {
            if ($mailNotifierStrategy->isAppropriate()) {
                $this->swiftMessage->setSubject($mailNotifierStrategy->getSubject());
                $this->errors = $mailNotifierStrategy->getErrors();
                $this->sendTo = $mailNotifierStrategy->getSendTo($this->configLoader);
                $bodyMessage = $mailNotifierStrategy->getBodyMessage();

                $this->contextualNotify($bodyMessage);
            }
        }
    }

    /**
     * @param string $bodyMessage
     */
    private function contextualNotify($bodyMessage)
    {
        $this->swiftMessage
            ->setTo($this->sendTo);

        $handler = $this->factory->createMailHandler($this->swiftMessage);

        $this->logger->pushHandler($handler);

        $message = $bodyMessage;

        foreach ($this->errors as $error) {
            $message = $message . $error->getMessage() . "\n\n";
        }

        $handler->setFormatter($this->formatter);

        $this->logger->debug($message);
        $this->logger->critical($message);
    }
}