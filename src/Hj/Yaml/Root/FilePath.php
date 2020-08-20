<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:37
 */

namespace Hj\Yaml\Root;

/**
 * Class FilePath
 * @package Hj\Yaml\Root
 */
class FilePath extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'filePath';
    }
}