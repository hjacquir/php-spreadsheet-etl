<?php
/**
 * User: h.jacquir
 * Date: 19/02/2020
 * Time: 16:58
 */

namespace Hj\File\Field;

/**
 * Class BirthDate
 * @package Hj\File\Field
 */
class BirthDate extends AbstractField
{
    const DATE_DATABASE_FORMAT = 'd/m/Y';
    const DATE_FILE_INPUT_FORMAT = 'd/m/Y H:i:s';

    /**
     * Return the expected header title
     *
     * @return string
     */
    public function getExpectedHeaderValue()
    {
        return "BIRTHDATE";
    }
}