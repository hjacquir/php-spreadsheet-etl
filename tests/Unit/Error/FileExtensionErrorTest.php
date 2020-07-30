<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 14:34
 */

namespace Hj\Tests\Unit\Error;

use Hj\Error\Error;
use Hj\Error\FileExtensionError;
use PHPUnit\Framework\TestCase;

/**
 * Class FileExtensionErrorTest
 * @covers \Hj\Error\FileExtensionError
 */
class FileExtensionErrorTest extends TestCase
{
    /**
     * @var FileExtensionError
     */
    private $error;

    public function setUp()
    {
        $this->error = new FileExtensionError();
    }

    public function testGetLevelReturnCritical()
    {
        self::assertSame(Error::CRITICAL, $this->error->getLevel());
    }

    public function testGetMessage()
    {
        self::assertSame("Spreadsheet-etl had encountered an error. File format is not supported.", $this->error->getMessage());
    }

    public function testGetTargetReturnUser()
    {
        self::assertSame(Error::TARGET_USER, $this->error->target());
    }
}