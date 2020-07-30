<?php
/**
 * User: h.jacquir
 * Date: 19/02/2020
 * Time: 15:41
 */

namespace Hj\Validator\Data;

use Hj\File\CellAdapter;

/**
 * Class DataMandatoryValidator
 * @package Hj\Validator\Data
 */
class DataMandatoryValidator extends AbstractDataValidator
{
    /**
     * @param CellAdapter $cellAdapter
     * @return bool
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        $currentValue = $cellAdapter->getNormalizedValue();

        if ($currentValue === null || $currentValue === '') {
            return false;
        }

        return true;
    }
}