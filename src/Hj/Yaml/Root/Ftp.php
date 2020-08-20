<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:51
 */

namespace Hj\Yaml\Root;

/**
 * Class Ftp
 * @package Hj\Yaml\Root
 */
class Ftp extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'ftp';
    }
}