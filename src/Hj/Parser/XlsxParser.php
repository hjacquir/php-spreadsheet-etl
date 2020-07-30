<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:28
 */

namespace Hj\Parser;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\SpoutException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\Exception\InvalidSheetNameException;
use Box\Spout\Writer\Exception\SheetNotFoundException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\WriterInterface;
use Box\Spout\Writer\XLSX\Writer;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class XlsxParser
 * @package Hj\Parser
 */
class XlsxParser extends AbstractParser
{
    /**
     * @param string $existingFilePath
     * @param string $tempFilePath
     * @param array $newRow
     * @throws IOException
     * @throws InvalidSheetNameException
     * @throws SheetNotFoundException
     * @throws SpoutException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function addOneRowToExistingFile($existingFilePath, $tempFilePath, array $newRow)
    {
        $reader = $this->getBoxSpoutBasedReader();
        $writer = $this->getBoxSpoutWriter();

        $reader->open($existingFilePath);
        $writer->openToFile($tempFilePath);

        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            $originalSheetName = $sheet->getName();
            // Add sheets in the new file, as we read new sheets in the existing one
            if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                // ... and copy each row into the new spreadsheet
                $writer->addRow($row);
            }
            // save original name of the sheet
            $sheet = $writer->getCurrentSheet();
            $sheet->setName($originalSheetName);
        }

        foreach ($writer->getSheets() as $sheet) {
            $writer->setCurrentSheet($sheet);
        }

        // At this point, the new spreadsheet contains the same data as the existing one.
        // So let's add the new data:
        $writer->addRow($newRow);

        $reader->close();
        $writer->close();

        unlink($existingFilePath);
        rename($tempFilePath, $existingFilePath);
    }

    /**
     * @return array
     */
    public function getSupportedFileExtensions()
    {
        return [
            'xlsx',
            'XLSX',
        ];
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getContextualReader()
    {
        return IOFactory::createReader("Xlsx");
    }

    /**
     * @return \Box\Spout\Reader\ReaderInterface
     * @throws UnsupportedTypeException
     */
    private function getBoxSpoutBasedReader()
    {
        return ReaderFactory::create(Type::XLSX);
    }

    /**
     * @return Writer | WriterInterface
     * @throws UnsupportedTypeException
     */
    private function getBoxSpoutWriter()
    {
        return WriterFactory::create(Type::XLSX);
    }
}