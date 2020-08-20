<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:05
 */

namespace Hj\Yaml\Child;

/**
 * Class DbName
 * @package Hj\Yaml\Child
 */
class DbName extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'dbname';
    }
}