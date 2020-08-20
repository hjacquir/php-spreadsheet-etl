<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:04
 */

namespace Hj\Yaml\Child;

/**
 * Class Charset
 * @package Hj\Yaml\Child
 */
class Charset extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'charset';
    }
}