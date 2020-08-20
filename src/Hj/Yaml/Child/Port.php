<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:56
 */

namespace Hj\Yaml\Child;

/**
 * Class Port
 * @package Hj\Yaml\Child
 */
class Port extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'port';
    }
}