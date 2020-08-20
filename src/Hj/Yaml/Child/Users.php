<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:02
 */

namespace Hj\Yaml\Child;

/**
 * Class Users
 * @package Hj\Yaml\Child
 */
class Users extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'users';
    }
}