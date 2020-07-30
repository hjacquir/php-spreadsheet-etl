<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 08:52
 */

namespace Hj\File\Field;

use Hj\File\CellAdapter;
use Hj\Validator\Data\AbstractDataValidator;

/**
 * Class AbstractField
 * @package Hj\File\Field
 */
abstract class AbstractField implements Field
{
    /**
     * @var AbstractDataValidator[]
     */
    private $validators = [];

    /**
     * Check if the field'value is valid or not
     *
     * @param CellAdapter $cellAdapter
     */
    public function validValue(CellAdapter $cellAdapter)
    {
        foreach ($this->validators as $validator) {
            $validator->valid($cellAdapter);
        }
    }

    /**
     * @param AbstractDataValidator $validator
     */
    public function addValidator(AbstractDataValidator $validator)
    {
        array_push($this->validators, $validator);
    }

    /**
     * @return AbstractDataValidator[]
     */
    public function getValidator()
    {
        return $this->validators;
    }


    /**
     * @param CellAdapter $cellAdapter
     * @return mixed
     */
    public function getCurrentValue(CellAdapter $cellAdapter)
    {
        return $cellAdapter->getCell()->getValue();
    }
}