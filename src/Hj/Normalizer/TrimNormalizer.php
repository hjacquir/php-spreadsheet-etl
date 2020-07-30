<?php
/**
 * User: h.jacquir
 * Date: 04/02/2020
 * Time: 09:05
 */

namespace Hj\Normalizer;

/**
 * Class TrimNormalizer
 * @package Hj\Normalizer
 */
class TrimNormalizer implements Normalizer
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        $charList = " \t\n\r\0\x0B\"\'\;\,\.\+\-\_\?\!\&\:";

        $trimmed =  trim($value, $charList);
        $trimmed = trim($trimmed, ' / ');

        return $trimmed;
    }
}