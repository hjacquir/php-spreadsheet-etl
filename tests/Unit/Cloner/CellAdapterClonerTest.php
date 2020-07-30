<?php
/**
 * User: h.jacquir
 * Date: 26/02/2020
 * Time: 16:40
 */

namespace Hj\Tests\Unit\Cloner;

use Hj\Cloner\CellAdapterCloner;
use Hj\File\CellAdapter;
use Hj\Helper\Tests\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CellAdapterClonerTest extends AbstractTestCase
{
    /**
     * @var CellAdapterCloner
     */
    private $currentTested;

    /**
     * @var CellAdapter|MockObject
     */
    private $cellAdapter;

    public function setUp()
    {
        $this->cellAdapter = $this->getMockConstructorDisabled(CellAdapter::class);

        $this->currentTested = new CellAdapterCloner($this->cellAdapter);
    }

    public function testReplicateShouldReturnAnClonedCellAdapter()
    {
        self::assertInstanceOf("Hj\File\CellAdapter", $this->currentTested->replicate());
    }
}