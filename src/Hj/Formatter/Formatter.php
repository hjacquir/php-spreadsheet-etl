<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 09:09
 */

namespace Hj\Formatter;

/**
 * Interface Formatter
 * @package Hj\Formatter
 */
interface Formatter
{
    /**
     * @param $value
     * @return mixed
     */
    public function format($value);
}