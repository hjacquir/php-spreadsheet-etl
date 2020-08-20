<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:38
 */

namespace Hj\Yaml\Child;

/**
 * Class Waiting
 * @package Hj\Yaml\Child
 */
class Waiting extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'waiting';
    }
}