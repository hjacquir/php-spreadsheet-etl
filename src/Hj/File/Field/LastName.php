<?php
/**
 * User: h.jacquir
 * Date: 13/02/2020
 * Time: 15:17
 */

namespace Hj\File\Field;

/**
 * Class LastName
 * @package Hj\File\Field
 */
class LastName extends AbstractField
{
    const EXPECTED_HEADER = "LASTNAME";
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