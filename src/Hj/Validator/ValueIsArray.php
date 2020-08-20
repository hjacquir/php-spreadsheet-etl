<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 10:23
 */

namespace Hj\Validator;

/**
 * Class ValueIsArray
 * @package Hj\Validator
 */
class ValueIsArray extends AbstractTypeValidator
{
    const EXPECTED_TYPE = "array";

    /**
     * @param $value
     * @return bool
     */
    public function valid($value)
    {
        return is_array($value);
    }

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return self::EXPECTED_TYPE;
    }
}