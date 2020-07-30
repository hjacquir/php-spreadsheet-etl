<?php
/**
 * User: h.jacquir
 * Date: 13/02/2020
 * Time: 15:17
 */

namespace Hj\File\Field;

/**
 * Class FirstName
 * @package Hj\File\Field
 */
class FirstName extends AbstractField
{
    const EXPECTED_HEADER = "FIRSTNAME";
    const MAXIMAL_LENGTH = 20;

    /**
     * Return the expected header title
     *
     * @return string
     */
    public function getExpectedHeaderValue()
    {
        return self::EXPECTED_HEADER;
    }
}