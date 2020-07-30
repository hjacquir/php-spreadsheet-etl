<?php
/**
 * User: h.jacquir
 * Date: 29/01/2020
 * Time: 09:10
 */

namespace Hj;

use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\GettingSheetFromFileError;
use Hj\Error\File\LoadingFileError;
use Hj\Helper\CatchedErrorHandler;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

/**
 * Extractor based on PhpOffice\PhpSpreadsheet library
 *
 * Class Extractor
 * @package Hj
 */
class Extractor
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var CellIterator
     */
    private $cellIterator;

    /**
     * @var Cell
     */
    private $currentCell;

    /**
     * @var Row
     */
    private $firstRow;

    /**
     * @var Cell[]
     */
    private $extractedHeader = [];

    /**
     * @var GettingSheetFromFileError
     */
    private $gettingSheetFromFileError;

    /**
     * @var LoadingFileError
     */
    private $loadingFileError;

    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * Extractor constructor.
     * @param WaitingDirectory $waitingDirectory
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param LoadingFileError $loadingFileError
     * @param GettingSheetFromFileError $gettingSheetFromFileError
     * @param BaseDirectory $inProcessingDirectory
     */
    public function __construct(
        WaitingDirectory $waitingDirectory,
        CatchedErrorHandler $catchedErrorHandler,
        LoadingFileError $loadingFileError,
        GettingSheetFromFileError $gettingSheetFromFileError,
        BaseDirectory $inProcessingDirectory
    ) {
        $this->catchedErrorHandler = $catchedErrorHandler;
        $this->loadingFileError = $loadingFileError;
        $this->gettingSheetFromFileError = $gettingSheetFromFileError;
        $this->inProcessingDirectory = $inProcessingDirectory;
        $this->waitingDirectory = $waitingDirectory;
    }

    /**
     * Extract only the header
     *
     * @param IReader $reader
     *
     * @return Cell[] An array of Cell
     */
    public function extractHeader(IReader $reader)
    {
        $cells = [];

        $rows = $this->getRows($reader, 1, 1);

        // only one row for the header
        $this->firstRow = current($rows);

        $this->cellIterator = $this->firstRow->getCellIterator();

        while ($this->cellIterator->valid()) {
            $this->currentCell = $this->cellIterator->current();

            if (false === $this->ignoreCell($this->currentCell->getValue())) {
                $currentColumnName = $this->currentCell->getColumn();
                $cells[$currentColumnName] = $this->currentCell;
            }

            $this->cellIterator->next();
        }

        $this->extractedHeader = $cells;

        return $cells;
    }

    /**
     * @return Cell[]
     */
    public function getExtractedHeader(): array
    {
        return $this->extractedHeader;
    }

    /**
     * @param IReader $reader
     * @return Cell[] An array of Cell
     */
    public function extractCells(IReader $reader)
    {
        $cells = [];

        // prevent extractHeader method never be called
        if (empty($this->extractedHeader)) {
            $this->extractedHeader = $this->extractHeader($reader);
        }

        $extractedRows = $this->extractRows($reader);

        foreach ($extractedRows as $key => $extractedRow) {
            $currentRowCells = [];
            $currentCellIterator = $extractedRow->getCellIterator();

            while ($currentCellIterator->valid()) {
                $currentCell = $currentCellIterator->current();
                $currentColumnName = $currentCell->getColumn();
                // we add the cell only if the column exist on the header
                if (array_key_exists($currentColumnName, $this->extractedHeader)) {
                    $currentRowCells[$currentColumnName] = $currentCell;
                }

                $currentCellIterator->next();
            }

            $cells[$key] = $currentRowCells;
        }

        return $cells;
    }

    /**
     * @param string $currentValue
     *
     * @return bool
     */
    private function ignoreCell($currentValue)
    {
        if ($currentValue === null || $currentValue === "") {
            return true;
        }

        return false;
    }

    /**
     * Extract all rows
     *
     * @param IReader $reader
     * @return Row[] An array of Row
     */
    private function extractRows(IReader $reader)
    {
        $extractedRows = $this->getRows($reader, 2);

        return $extractedRows;
    }

    /**
     * Return an array of Row
     *
     * @param IReader $reader
     * @param $startRowIndex
     * @param null $endRowIndex
     * @return Row[] An array of Row
     */
    private function getRows(IReader $reader, $startRowIndex, $endRowIndex = null)
    {
        $rows = [];

        $filePath = $this->inProcessingDirectory->getCurrentPoppedFileName();

        try {
            $spreadSheet = $reader->load($filePath);

            try {
                $currentSheet = $spreadSheet->getActiveSheet();
                $rowIterator = $currentSheet->getRowIterator($startRowIndex, $endRowIndex);

                while ($rowIterator->valid()) {
                    array_push($rows, $rowIterator->current());
                    $rowIterator->next();
                }
            } catch (\Exception $e) {
                $this->catchedErrorHandler->handleErrorOnGettingSheetFailure(
                    $e,
                    $this->gettingSheetFromFileError,
                    $this->waitingDirectory
                );
            }

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            $this->catchedErrorHandler->handleErrorOnFileLoadingFailure(
                $e,
                $this->loadingFileError,
                $this->waitingDirectory
            );
        } catch (\Exception $exception) {
            $this->catchedErrorHandler->handleErrorOnFileLoadingFailure(
                $exception,
                $this->loadingFileError,
                $this->waitingDirectory
            );
        }

        return $rows;
    }
}