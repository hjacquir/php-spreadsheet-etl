<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 09:10
 */

namespace Hj\Formatter;

use DateTime;

/**
 * Class DateFormatter
 * @package Hj\Formatter
 */
class DateFormatter implements Formatter
{
    const FORMAT = 'd/m/Y H:i:s';
    /**
     * @var DateTime
     */
    private $valueToFormat;

    /**
     * @param DateTime $value
     * @return mixed|string
     */
    public function format($value)
    {
        $this->valueToFormat = $value;

        return $this->valueToFormat->format(self::FORMAT);
    }
}