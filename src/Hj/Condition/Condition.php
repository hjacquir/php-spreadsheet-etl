<?php
/**
 * User: h.jacquir
 * Date: 23/03/2020
 * Time: 09:19
 */

namespace Hj\Condition;

/**
 * Interface Condition
 * @package Hj\Condition
 */
interface Condition
{
    /**
     * @return bool
     */
    public function isSatisfied();
}