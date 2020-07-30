<?php
/**
 * User: h.jacquir
 * Date: 14/02/2020
 * Time: 11:04
 */

namespace Hj\Strategy;

/**
 * Do nothing
 *
 * Class NullStrategy
 * @package Hj\Strategy
 */
class NullStrategy implements Strategy
{
    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false;
    }

    public function apply()
    {
    }
}