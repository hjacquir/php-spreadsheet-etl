<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 10:42
 */

namespace Hj\Tests\Unit\Factory;

use Hj\Factory\MailHandlerFactory;
use Monolog\Handler\SwiftMailerHandler;

/**
 * Class MailHandlerFactoryTest
 * @package Hj\Tests\Unit\Factory
 * @covers \Hj\Factory\MailHandlerFactory
 */
class MailHandlerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateSwiftHandlerReturnAnInstanceOfMailHandler()
    {
        $swiftMessage = $this->getMockBuilder(\Swift_Message::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory = new MailHandlerFactory($mailer);

        self::assertInstanceOf(SwiftMailerHandler::class, $factory->createMailHandler($swiftMessage));
    }
}