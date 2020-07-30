<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:13
 */

namespace Hj\Parser;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class CsvParser
 * @package Hj\Parser
 */
class CsvParser extends AbstractParser
{
    /**
     * @return array
     */
    public function getSupportedFileExtensions()
    {
        return [
            'csv',
            'CSV',
        ];
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getContextualReader()
    {
        return IOFactory::createReader("Csv");
    }
}