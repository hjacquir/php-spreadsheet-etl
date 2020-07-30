<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:25
 */

namespace Hj\Parser;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class OdsParser
 * @package Hj\Parser
 */
class OdsParser extends AbstractParser
{
    /**
     * @return array
     */
    public function getSupportedFileExtensions()
    {
        return [
            'ods',
            'ODS',
        ];
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getContextualReader()
    {
        return IOFactory::createReader("Ods");
    }
}