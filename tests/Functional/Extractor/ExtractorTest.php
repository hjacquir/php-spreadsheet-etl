<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 14:59
 */

namespace Hj\Tests\Functional\Extractor;

use Hj\Collector\CollectorIterator;
use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\DirectoryNotExistError;
use Hj\Error\File\GettingSheetFromFileError;
use Hj\Error\File\LoadingFileError;
use Hj\Extractor;
use Hj\Helper\CatchedErrorHandler;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ExtractorTest
 * @package Hj\Tests\Functional\Extractor
 * @covers \Hj\Extractor
 */
class ExtractorTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    public function setUp()
    {
        $this->errorCollector = new ErrorCollector(
            new CollectorIterator()
        );

        $this->catchedErrorHandler = new CatchedErrorHandler(
           $this->errorCollector
        );

    }

    public function testExtractHeaderReturnAnEmptyArrayWhenFileIsEmpty()
    {
        $reader = IOFactory::createReader("Xlsx");
        $inProcessingDir = __DIR__ . "/in_processing/emptyFile/";
        $inProcessingDirectory = new BaseDirectory(
            $inProcessingDir,
            $this->catchedErrorHandler,
            new DirectoryNotExistError()
        );

        $waitingDir = __DIR__ . "/waiting/withNullAndEmptyValue/";
        $waitingDirectory = new WaitingDirectory(
            new BaseDirectory(
                $waitingDir,
                $this->catchedErrorHandler,
                new DirectoryNotExistError()
            )
        );

        $extractor = $this->configureExtractor(
            $inProcessingDirectory,
            $waitingDirectory
        );

        $cells = $extractor->extractHeader($reader);
        self::assertSame(0, count($cells));
    }

    public function testExtractHeaderReturnAnArrayOfCellsWhenFileIsNotEmpty()
    {
        $reader = IOFactory::createReader("Csv");
        $inProcessingDir = __DIR__ . "/in_processing/withNullAndEmptyValue/";
        $inProcessingDirectory = new BaseDirectory(
            $inProcessingDir,
            $this->catchedErrorHandler,
            new DirectoryNotExistError()
        );

        $waitingDir = __DIR__ . "/waiting/withNullAndEmptyValue/";
        $waitingDirectory = new WaitingDirectory(
            new BaseDirectory(
                $waitingDir,
                $this->catchedErrorHandler,
                new DirectoryNotExistError()
            )
        );

        $extractor = $this->configureExtractor(
            $inProcessingDirectory,
            $waitingDirectory
        );

        $extractedHeader = $extractor->extractHeader($reader);

        $cellValues = [];

        foreach ($extractedHeader as $cell) {
            array_push($cellValues, $cell->getValue());
        }

        $expected = [
            'Header1',
            'Header2',
            'Header3',
            'Header4',
            'Header5',
            'Header6',
            'Header7',
        ];

        self::assertSame($expected, $cellValues);
    }

    /**
     * @param BaseDirectory $inProcessingDirectory
     * @param WaitingDirectory $waitingDirectory
     * @return Extractor
     */
    private function configureExtractor(
        BaseDirectory $inProcessingDirectory,
        WaitingDirectory $waitingDirectory
    )
    {
        return new Extractor(
            $waitingDirectory,
            new CatchedErrorHandler(
                new ErrorCollector(new CollectorIterator())
            ),
            new LoadingFileError(),
            new GettingSheetFromFileError(),
            $inProcessingDirectory
        );
    }

    public function testExtractCellsReturnAnArrayOfCellsWhenFileIsNotEmpty()
    {
        $reader = IOFactory::createReader("Csv");

        $inProcessingDir = __DIR__ . "/in_processing/withNullAndEmptyValue/";
        $inProcessingDirectory = new BaseDirectory(
            $inProcessingDir,
            $this->catchedErrorHandler,
            new DirectoryNotExistError()
        );

        $waitingDir = __DIR__ . "/waiting/withNullAndEmptyValue/";
        $waitingDirectory = new WaitingDirectory(
            new BaseDirectory(
                $waitingDir,
                $this->catchedErrorHandler,
                new DirectoryNotExistError()
            )
        );

        $extractor = $this->configureExtractor(
            $inProcessingDirectory,
            $waitingDirectory
        );

        $extractedCells = $extractor->extractCells($reader);
        $expectedNumberOfRowsExtracted = 2;
        self::assertSame($expectedNumberOfRowsExtracted, count($extractedCells));
        $cellValues = [];
        foreach ($extractedCells as $extractedCell) {
            $currentValues = [];
            /** @var Cell $cell */
            foreach ($extractedCell as $cell) {
                array_push($currentValues, $cell->getValue());
            }
            array_push($cellValues, $currentValues);
        }
        $expected = [
            [
                'zzz',
                'rrr',
                'ttt',
                'uyuty',
                'tytyu',
                'uuu',
                12,
            ],
            [
                'ppp',
                'ooo',
                false,
                false,
                '&&',
                'tt',
                'uu',
            ]
        ];

        self::assertSame($expected, $cellValues);
    }
}