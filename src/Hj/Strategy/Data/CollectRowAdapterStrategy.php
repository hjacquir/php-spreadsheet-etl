<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 11:24
 */

namespace Hj\Strategy\Data;

use Hj\Cloner\CellAdapterCloner;
use Hj\Cloner\RowAdapterCloner;
use Hj\Collector\ErrorCollector;
use Hj\Collector\RowCollector;
use Hj\Directory\BaseDirectory;
use Hj\File\CellAdapter;
use Hj\File\RowAdapter;
use Hj\Normalizer\AccentsRemoverNormalizer;
use Hj\Normalizer\DateStringExcelNormalizer;
use Hj\Normalizer\ToUpperNormalizer;
use Hj\Normalizer\TrimNormalizer;
use Hj\Strategy\Header\HeaderExtraction;
use Hj\Strategy\Strategy;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * Get all row from the file
 *
 * Class CollectRowAdapterStrategy
 * @package Hj\Strategy\Data
 */
class CollectRowAdapterStrategy implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var RowCollector
     */
    private $rowCollector;

    /**
     * @var RowAdapterCloner
     */
    private $rowAdapterCloner;

    /**
     * @var CellAdapterCloner
     */
    private $cellAdapterCloner;

    /**
     * @var RowsExtractionStrategy
     */
    private $rowsExtractionStrategy;

    /**
     * @var HeaderExtraction
     */
    private $headerExtractionStrategy;

    /**
     * @var TrimNormalizer
     */
    private $trimNormalizer;

    /**
     * @var AccentsRemoverNormalizer
     */
    private $accentsRemoverNormalizer;

    /**
     * @var ToUpperNormalizer
     */
    private $toUpperNormalizer;

    /**
     * @var DateStringExcelNormalizer
     */
    private $dateStringExcelNormalizer;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * CollectRowAdapterStrategy constructor.
     * @param ErrorCollector $errorCollector
     * @param DateStringExcelNormalizer $dateStringExcelNormalizer
     * @param BaseDirectory $inProcessingDir
     * @param RowCollector $rowCollector
     * @param RowAdapterCloner $rowAdapterCloner
     * @param CellAdapterCloner $cellAdapterCloner
     * @param RowsExtractionStrategy $rowsExtractionStrategy
     * @param HeaderExtraction $headerExtractionStrategy
     * @param TrimNormalizer $trimNormalizer
     * @param AccentsRemoverNormalizer $accentsRemoverNormalizer
     * @param ToUpperNormalizer $toUpperNormalizer
     */
    public function __construct(
        ErrorCollector $errorCollector,
        DateStringExcelNormalizer $dateStringExcelNormalizer,
        BaseDirectory $inProcessingDir,
        RowCollector $rowCollector,
        RowAdapterCloner $rowAdapterCloner,
        CellAdapterCloner $cellAdapterCloner,
        RowsExtractionStrategy $rowsExtractionStrategy,
        HeaderExtraction $headerExtractionStrategy,
        TrimNormalizer $trimNormalizer,
        AccentsRemoverNormalizer $accentsRemoverNormalizer,
        ToUpperNormalizer $toUpperNormalizer
    ) {
        $this->errorCollector = $errorCollector;
        $this->dateStringExcelNormalizer = $dateStringExcelNormalizer;
        $this->inProcessingDir = $inProcessingDir;
        $this->rowCollector = $rowCollector;
        $this->rowAdapterCloner = $rowAdapterCloner;
        $this->cellAdapterCloner = $cellAdapterCloner;
        $this->rowsExtractionStrategy = $rowsExtractionStrategy;
        $this->headerExtractionStrategy = $headerExtractionStrategy;
        $this->trimNormalizer = $trimNormalizer;
        $this->accentsRemoverNormalizer = $accentsRemoverNormalizer;
        $this->toUpperNormalizer = $toUpperNormalizer;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDir->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        $extractedHeaders = $this->headerExtractionStrategy->getExtractedHeaderValues();
        $initialExtractedHeaderValues = $this->headerExtractionStrategy->getInitialExtractedHeaderValues();

        foreach ($this->rowsExtractionStrategy->getExtractedRows() as $key => $row) {
            // we need to use a clone of original object to avoid to create an new instance
            /** @var RowAdapter $rowAdapter */
            $rowAdapter = $this->rowAdapterCloner->replicate();
            $currentRowIndex = $key + 2;

            $rowAdapter->setIndex($currentRowIndex);
            $currentCellIndex = 0;

            /**
             * @var string $columnName
             * @var Cell $cell
             */
            foreach ($row as $columnName => $cell) {
                // we need to use a clone of original object to avoid to create an new instance
                /** @var CellAdapter $cellAdapter */
                $cellAdapter = $this->cellAdapterCloner->replicate();

                $cellAdapter->setRowIndex($currentRowIndex);
                $cellAdapter->setColumnName($columnName);
                $cellAdapter->setCell($cell);
                $cellAdapter->setAssociatedHeader($extractedHeaders[$currentCellIndex]);
                $cellAdapter->setInitialAssociatedHeader($initialExtractedHeaderValues[$currentCellIndex]);

                $dateNormalizedValue = $this->dateStringExcelNormalizer->normalize($cellAdapter);
                $trimmedValue = $this->trimNormalizer->normalize($dateNormalizedValue);
                $accentRemovedValue = $this->accentsRemoverNormalizer->normalize($trimmedValue);
                $upperCasedValue = $this->toUpperNormalizer->normalize($accentRemovedValue);

                $cellAdapter->setNormalizedValue($upperCasedValue);

                $cellAdapter->setRowAdapter($rowAdapter);
                // add cell to row adapter
                $rowAdapter->addCell($cellAdapter);
                $currentCellIndex++;
            }
            // add current row adapter to row collector
            $this->rowCollector->addRow($rowAdapter);
        }
    }

    /**
     * @return RowCollector
     */
    public function getRowCollector()
    {
        $this->rowCollector->rewind();

        return $this->rowCollector;
    }
}