<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:49
 */

namespace Hj\Yaml\Child;

/**
 * Class Failure
 * @package Hj\Yaml\Child
 */
class Failure extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return "failure";
    }
}