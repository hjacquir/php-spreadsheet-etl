<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:50
 */

namespace Hj\Yaml\Root;

/**
 * Class Smtp
 * @package Hj\Yaml\Root
 */
class Smtp extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'smtp';
    }
}