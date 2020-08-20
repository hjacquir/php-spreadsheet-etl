<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:48
 */

namespace Hj\Yaml\Child;

/**
 * Class InProcessing
 * @package Hj\Yaml\Child
 */
class InProcessing extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'in_processing';
    }
}