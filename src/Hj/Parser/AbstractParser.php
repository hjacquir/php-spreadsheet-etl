<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:14
 */

namespace Hj\Parser;

use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\GettingSheetFromFileError;
use Hj\Error\File\LoadingFileError;
use Hj\Error\File\OnSettingValueError;
use Hj\Extractor;
use Hj\Helper\CatchedErrorHandler;
use Hj\Normalizer\AccentsRemoverNormalizer;
use Hj\Normalizer\ToUpperNormalizer;
use Hj\Normalizer\TrimNormalizer;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class AbstractParser
 * @package Hj\Parser
 */
abstract class AbstractParser implements Parser
{
    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    /**
     * @var GettingSheetFromFileError
     */
    private $gettingSheetFromFileError;

    /**
     * @var LoadingFileError
     */
    private $loadingFileError;

    /**
     * @var OnSettingValueError
     */
    private $onSettingValueError;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * @var AccentsRemoverNormalizer
     */
    private $accentsRemoverNormalizer;

    /**
     * @var ToUpperNormalizer
     */
    private $toUpperNormalizer;

    /**
     * @var TrimNormalizer
     */
    private $trimNormalizer;

    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * AbstractParser constructor.
     * @param WaitingDirectory $waitingDirectory
     * @param OnSettingValueError $onSettingValueError
     * @param LoadingFileError $loadingFileError
     * @param GettingSheetFromFileError $gettingSheetFromFileError
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param BaseDirectory $inProgressDir
     * @param Extractor $extractor
     * @param AccentsRemoverNormalizer $accentsRemoverNormalizer
     * @param ToUpperNormalizer $toUpperNormalizer
     * @param TrimNormalizer $trimNormalizer
     */
    public function __construct(
        WaitingDirectory $waitingDirectory,
        OnSettingValueError $onSettingValueError,
        LoadingFileError $loadingFileError,
        GettingSheetFromFileError $gettingSheetFromFileError,
        CatchedErrorHandler $catchedErrorHandler,
        BaseDirectory $inProgressDir,
        Extractor $extractor,
        AccentsRemoverNormalizer $accentsRemoverNormalizer,
        ToUpperNormalizer $toUpperNormalizer,
        TrimNormalizer $trimNormalizer
    ) {
        $this->waitingDirectory = $waitingDirectory;
        $this->onSettingValueError = $onSettingValueError;
        $this->loadingFileError = $loadingFileError;
        $this->gettingSheetFromFileError = $gettingSheetFromFileError;
        $this->catchedErrorHandler = $catchedErrorHandler;
        $this->inProcessingDirectory = $inProgressDir;
        $this->extractor = $extractor;
        $this->accentsRemoverNormalizer = $accentsRemoverNormalizer;
        $this->toUpperNormalizer = $toUpperNormalizer;
        $this->trimNormalizer = $trimNormalizer;
    }

    /**
     * Return true if header is on first row
     *
     * @return bool
     */
    public function checkIfHeaderIsOnFirstRow()
    {
        $currentCellValue = null;

        $filePath = $this->inProcessingDirectory->getCurrentPoppedFileName();

        /** @var IReader $reader */
        $reader = $this->getReader();

        try {
            /** @var Spreadsheet $spreadSheet */
            $spreadSheet = $reader->load($filePath);

            try {
                $currentSheet = $spreadSheet->getActiveSheet();
                $currentRow = $currentSheet->getRowIterator(1, 1)->current();
                $currentCellValue = $currentRow->getCellIterator()->current()->getValue();
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

        return $currentCellValue !== null;
    }

    /**
     * Return true if the file has multiple sheet
     *
     * @return bool
     */
    public function checkIfFileHasMultipleSheet()
    {
        $filePath = $this->inProcessingDirectory->getCurrentPoppedFileName();
        /** @var IReader $reader */
        $reader = $this->getReader();

        $sheets = [];

        /** @var  Spreadsheet $spreadSheets */
        try {
            $spreadSheets = $reader->load($filePath);
            /** @var array $sheets */
            $sheets = $spreadSheets->getAllSheets();
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

        return count($sheets) > 1;
    }

    /**
     * @return IReader
     */
    public function getReader()
    {
        $reader = $this->getContextualReader();
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);

        return $reader;
    }

    /**
     * @return Cell[]
     */
    public function getNormalizedHeader()
    {
        $initialHeader = $this->getInitialHeader();

        $normalizedHeader = $this->normalize($initialHeader);

        return $normalizedHeader;
    }

    /**
     * @return Cell[]
     */
    public function getInitialHeader()
    {
        return $this->extractor->extractHeader($this->getReader());
    }

    /**
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->extractor->extractCells($this->getReader());
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return in_array(
            $this->inProcessingDirectory->getCurrentPoppedFileExtension(),
            $this->getSupportedFileExtensions()
        );
    }

    /**
     * @return array
     */
    public abstract function getSupportedFileExtensions();

    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     */
    protected abstract function getContextualReader();

    /**
     * @param Cell[] $header
     * @return Cell[]
     */
    private function normalize(array $header)
    {
        $normalizedValues = [];

        // on extrait la valeur de la cellule et on retourne uniquement cette valeur
        foreach ($header as $key => $cell) {
            $value = $cell->getValue();
            // normalize only cell value not null because when value is null -> parent cell instance
            // is null and setValue method throw an exception when called to a null objet
            if (null !== $value) {
                $value = $this->trimNormalizer->normalize($value);
                $value = $this->accentsRemoverNormalizer->normalize($value);
                $value = $this->toUpperNormalizer->normalize($value);

                try {
                    $cell->setValue($value);
                } catch (\Exception $e) {
                    $this->catchedErrorHandler->handleErrorOnSettingCellValue(
                        $e,
                        $this->onSettingValueError,
                        $this->waitingDirectory
                    );
                }
            }

            $normalizedValues[$key] = $cell;
        }

        return $normalizedValues;
    }
}