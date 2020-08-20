<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 09:24
 */

namespace Hj\Yaml\Child;

/**
 * Class User
 * @package Hj\Yaml\Child
 */
class User extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'user';
    }
}