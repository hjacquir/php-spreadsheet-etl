<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 16:39
 */

namespace Hj\Yaml\Child;

/**
 * Class Url
 * @package Hj\Yaml\Child
 */
class Url extends AbstractChildComponent
{
    /**
     * @return string|void
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return "url";
    }
}