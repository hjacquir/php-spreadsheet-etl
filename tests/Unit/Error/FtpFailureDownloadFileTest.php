<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 14:51
 */

namespace Hj\Tests\Unit\Error;

use Hj\Error\Error;
use Hj\Error\FtpFailureDownloadFile;
use PHPUnit\Framework\TestCase;

/**
 * Class FtpFailureDownloadFileTest
 * @package Hj\Tests\Unit\Error
 * @covers \Hj\Error\FtpFailureDownloadFile
 */
class FtpFailureDownloadFileTest extends TestCase
{
    /**
     * @var FtpFailureDownloadFile
     */
    private $error;

    public function setUp()
    {
        $this->error = new FtpFailureDownloadFile();
    }

    public function testGetLevelReturnCritical()
    {
        self::assertSame(Error::CRITICAL, $this->error->getLevel());
    }

    public function testGetMessageWithDirName()
    {
        $this->error->setDirName("bla");
        self::assertSame("Spreadsheet-etl tried to download the files from the remote ftp : bla directory and encountered an error.", $this->error->getMessage());
    }

    public function testTargetReturnAdmin()
    {
        self::assertSame(Error::TARGET_ADMIN, $this->error->target());
    }
}