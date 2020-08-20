<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:00
 */

namespace Hj\Yaml\Child;

/**
 * Class Admins
 * @package Hj\Yaml\Child
 */
class Admins extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'admins';
    }
}