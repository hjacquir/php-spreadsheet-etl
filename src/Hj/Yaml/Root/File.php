<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:53
 */

namespace Hj\Yaml\Root;

/**
 * Class File
 * @package Hj\Yaml\Root
 */
class File extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return "file";
    }
}