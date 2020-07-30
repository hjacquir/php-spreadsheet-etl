<?php
/**
 * User: h.jacquir
 * Date: 08/04/2020
 * Time: 10:19
 */

namespace Hj\Normalizer;

/**
 * Normalize float number by replacing decimal with dot
 *
 * Class FloatNormalizer
 *
 * @package Hj\Normalizer
 */
class FloatNormalizer implements Normalizer
{
    /**
     * @param string $value
     * @return float
     */
    public function normalize($value)
    {
        $value = str_replace(",", ".", $value);
        $value = preg_replace('/\.(?=.*\.)/', '', $value);

        return floatval($value);
    }
}