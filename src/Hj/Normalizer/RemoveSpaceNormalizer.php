<?php
/**
 * User: h.jacquir
 * Date: 25/02/2020
 * Time: 09:29
 */

namespace Hj\Normalizer;

/**
 * Class RemoveSpaceNormalizer
 * @package Hj\Normalizer
 */
class RemoveSpaceNormalizer implements Normalizer
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        return str_replace(' ', '', $value);
    }
}