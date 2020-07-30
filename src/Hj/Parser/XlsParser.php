<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:26
 */

namespace Hj\Parser;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class XlsParser
 * @package Hj\Parser
 */
class XlsParser extends AbstractParser
{
    /**
     * @return array
     */
    public function getSupportedFileExtensions()
    {
        return [
            'xls',
            'XLS',
        ];
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getContextualReader()
    {
        return IOFactory::createReader("Xls");
    }
}