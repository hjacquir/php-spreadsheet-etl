<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:54
 */

namespace Hj\Yaml\Child;

/**
 * Class UserName
 * @package Hj\Yaml\Child
 */
class UserName extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return "username";
    }
}