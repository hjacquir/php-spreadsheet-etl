<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 10:24
 */

namespace Hj\Validator;

/**
 * Class ValueIsString
 * @package Hj\Validator
 */
class ValueIsString extends AbstractTypeValidator
{
    const EXPECTED_TYPE = "string";

    /**
     * @param $value
     * @return bool
     */
    public function valid($value)
    {
        return is_string($value);
    }

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return self::EXPECTED_TYPE;
    }
}