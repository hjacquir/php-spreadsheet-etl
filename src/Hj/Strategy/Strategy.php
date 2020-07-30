<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 14:01
 */

namespace Hj\Strategy;

/**
 * Interface Strategy
 * @package Hj\Strategy
 */
interface Strategy
{
    /**
     * @return bool
     */
    public function isAppropriate();

    public function apply();
}