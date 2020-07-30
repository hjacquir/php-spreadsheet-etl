<?php
/**
 * User: h.jacquir
 * Date: 11/03/2020
 * Time: 16:20
 */

namespace Hj;

/**
 * By pass validation if value is in the list
 *
 * Class ValidationByPasser
 * @package Hj
 */
class ValidationByPasser
{
    /**
     * @var
     */
    private $byPassedValues = [];

    /**
     * ByPassValidator constructor.
     * @param array $byPassedValues
     */
    public function __construct(array $byPassedValues)
    {
        $this->byPassedValues = $byPassedValues;
    }

    public function bypassValidation($value)
    {
        if (in_array($value, $this->byPassedValues)) {
            return true;
        }

        return false;
    }
}