<?php
/**
 * User: h.jacquir
 * Date: 03/02/2020
 * Time: 11:21
 */

namespace Hj\Normalizer;

/**
 * Class ToUpperNormalizer
 * @package Hj\Normalizer
 */
class ToUpperNormalizer implements Normalizer
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        return strtoupper($value);
    }
}