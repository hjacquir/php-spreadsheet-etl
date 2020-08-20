<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:55
 */

namespace Hj\Yaml\Child;

/**
 * Class Password
 * @package Hj\Yaml\Child
 */
class Password extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'password';
    }
}