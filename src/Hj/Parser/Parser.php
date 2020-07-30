<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 17:11
 */

namespace Hj\Parser;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * Interface Parser
 * @package Hj\Parser
 */
interface Parser
{
    public function getInitialHeader();

    public function getReader();

    public function getNormalizedHeader();

    public function isAppropriate();

    /**
     * @return bool
     */
    public function checkIfFileHasMultipleSheet();

    /**
     * @return bool
     */
    public function checkIfHeaderIsOnFirstRow();

    /**
     * @return Cell[]
     */
    public function getCells();
}