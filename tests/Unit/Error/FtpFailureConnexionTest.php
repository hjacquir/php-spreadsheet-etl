<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 14:34
 */

namespace Hj\Tests\Unit\Error;

use Exception;
use Hj\Error\Error;
use Hj\Error\FtpFailureConnexion;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FtpFailureConnexionTest
 * @covers \Hj\Error\FtpFailureConnexion
 */
class FtpFailureConnexionTest extends TestCase
{
    /**
     * @var FtpFailureConnexion
     */
    private $error;

    /**
     * @var Exception|MockObject
     */
    private $exception;

    public function setUp()
    {
        $this->exception = $this->getMockBuilder(Exception::class)
            ->getMock();

        $this->error = new FtpFailureConnexion($this->exception);
    }

    public function testGetLevelReturnCritical()
    {
        self::assertSame(Error::CRITICAL, $this->error->getLevel());
    }

    public function testGetMessage()
    {
        self::assertSame("Spreadsheet-etl encountered an error while connecting to the FTP server. Please check the server FTP settings : ", $this->error->getMessage());
    }

    public function testGetTargetReturnAdmin()
    {
        self::assertSame(Error::TARGET_ADMIN, $this->error->target());
    }
}