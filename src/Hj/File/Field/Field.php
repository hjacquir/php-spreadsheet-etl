<?php
/**
 * User: h.jacquir
 * Date: 13/01/2020
 * Time: 15:07
 */

namespace Hj\File\Field;

use Hj\File\CellAdapter;
use Hj\Validator\Data\AbstractDataValidator;

/**
 * Class Field
 * @package Hj\File\Field
 */
interface Field
{
    /**
     * Check if the  field'value is valid or not
     *
     * @param CellAdapter $cellAdapter
     */
    public function validValue(CellAdapter $cellAdapter);

    /**
     * Return the expected header title
     *
     * @return string
     */
    public function getExpectedHeaderValue();

    /**
     * @param CellAdapter $cellAdapter
     * @return mixed
     */
    public function getCurrentValue(CellAdapter $cellAdapter);

    /**
     * @param AbstractDataValidator $validator
     */
    public function addValidator(AbstractDataValidator $validator);

    /**
     * @return AbstractDataValidator[]
     */
    public function getValidator();
}