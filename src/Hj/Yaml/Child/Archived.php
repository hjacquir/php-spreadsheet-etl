<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:47
 */

namespace Hj\Yaml\Child;

/**
 * Class Archived
 * @package Hj\Yaml\Child
 */
class Archived extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'archived';
    }
}