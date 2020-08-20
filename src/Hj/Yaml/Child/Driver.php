<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:03
 */

namespace Hj\Yaml\Child;

/**
 * Class Driver
 * @package Hj\Yaml\Child
 */
class Driver extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'driver';
    }
}