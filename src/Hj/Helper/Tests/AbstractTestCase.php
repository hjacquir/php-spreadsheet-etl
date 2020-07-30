<?php
/**
 * User: h.jacquir
 * Date: 11/02/2020
 * Time: 15:27
 */

namespace Hj\Helper\Tests;

use Hj\Cloner\CellAdapterCloner;
use Hj\Cloner\RowAdapterCloner;
use Hj\Collector\ErrorCollector;
use Hj\Collector\RowCollector;
use Hj\Directory\BaseDirectory;
use Hj\Normalizer\AccentsRemoverNormalizer;
use Hj\Normalizer\RemoveSpaceNormalizer;
use Hj\Normalizer\ToUpperNormalizer;
use Hj\Normalizer\TrimNormalizer;
use Hj\Strategy\Data\RowsExtractionStrategy;
use Hj\Strategy\Header\HeaderExtraction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Helper class for test
 *
 * Class AbstractTestCase
 * @package Hj\Helper\Tests
 */
class AbstractTestCase extends TestCase
{
    /**
     * @var BaseDirectory|MockObject
     */
    private $inProcessingDir;

    /**
     * @var RowCollector|MockObject
     */
    private $rowCollector;

    /**
     * @var RowAdapterCloner|MockObject
     */
    private $rowAdapterCloner;

    /**
     * @var CellAdapterCloner|MockObject
     */
    private $cellAdapterCloner;

    /**
     * @var RowsExtractionStrategy|MockObject
     */
    private $rowsExtractionStrategy;

    /**
     * @var HeaderExtraction|MockObject
     */
    private $headerExtractionStrategy;

    /**
     * @var TrimNormalizer|MockObject
     */
    private $trimNormalizer;

    /**
     * @var AccentsRemoverNormalizer|MockObject
     */
    private $accentsRemoverNormalizer;

    /**
     * @var RemoveSpaceNormalizer|MockObject
     */
    private $spaceRemoverNormalizer;

    /**
     * @var ToUpperNormalizer|MockObject
     */
    private $toUpperNormalizer;

    public function setUp()
    {
        $this->inProcessingDir = $this->getMockConstructorDisabled(BaseDirectory::class);
        $this->rowCollector = $this->getMockConstructorDisabled(RowCollector::class);
        $this->rowAdapterCloner = $this->getMockConstructorDisabled(RowAdapterCloner::class);
        $this->cellAdapterCloner = $this->getMockConstructorDisabled(CellAdapterCloner::class);
        $this->rowsExtractionStrategy = $this->getMockConstructorDisabled(RowsExtractionStrategy::class);
        $this->headerExtractionStrategy = $this->getMockConstructorDisabled(HeaderExtraction::class);
        $this->accentsRemoverNormalizer = $this->getMockConstructorDisabled(AccentsRemoverNormalizer::class);
        $this->trimNormalizer = $this->getMockConstructorDisabled(TrimNormalizer::class);
        $this->spaceRemoverNormalizer = $this->getMockConstructorDisabled(RemoveSpaceNormalizer::class);
        $this->toUpperNormalizer = $this->getMockConstructorDisabled(ToUpperNormalizer::class);
    }

    /**
     * @return ErrorCollector|MockObject
     */
    public function getMockErrorCollector()
    {
        return $this->getMockConstructorDisabled(ErrorCollector::class);
    }

    /**
     * @return TrimNormalizer|MockObject
     */
    public function getTrimNormalizer()
    {
        return $this->trimNormalizer;
    }

    /**
     * @return AccentsRemoverNormalizer|MockObject
     */
    public function getAccentsRemoverNormalizer()
    {
        return $this->accentsRemoverNormalizer;
    }

    /**
     * @return RemoveSpaceNormalizer|MockObject
     */
    public function getSpaceRemoverNormalizer()
    {
        return $this->spaceRemoverNormalizer;
    }

    /**
     * @return ToUpperNormalizer|MockObject
     */
    public function getToUpperNormalizer()
    {
        return $this->toUpperNormalizer;
    }



    public function expectInProcessingDirHasFilesReturnTrue()
    {
        $this->inProcessingDir
            ->method('hasFiles')
            ->willReturn(true);
    }

    /**
     * @return BaseDirectory|MockObject
     */
    public function getInProcessingDir()
    {
        return $this->inProcessingDir;
    }

    /**
     * @return RowCollector|MockObject
     */
    public function getRowCollector()
    {
        return $this->rowCollector;
    }

    /**
     * @return RowAdapterCloner|MockObject
     */
    public function getRowAdapterCloner()
    {
        return $this->rowAdapterCloner;
    }

    /**
     * @return CellAdapterCloner|MockObject
     */
    public function getCellAdapterCloner()
    {
        return $this->cellAdapterCloner;
    }

    /**
     * @return RowsExtractionStrategy|MockObject
     */
    public function getRowsExtractionStrategy()
    {
        return $this->rowsExtractionStrategy;
    }

    /**
     * @return HeaderExtraction|MockObject
     */
    public function getHeaderExtractionStrategy()
    {
        return $this->headerExtractionStrategy;
    }

    /**
     * @param string $className
     * @return MockObject
     */
    protected function getMockConstructorDisabled($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}