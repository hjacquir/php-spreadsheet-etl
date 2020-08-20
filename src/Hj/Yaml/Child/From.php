<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:02
 */

namespace Hj\Yaml\Child;

/**
 * Class From
 * @package Hj\Yaml\Child
 */
class From extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'from';
    }
}