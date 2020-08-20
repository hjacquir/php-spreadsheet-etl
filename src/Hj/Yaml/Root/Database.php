<?php
/**
 * User: h.jacquir
 * Date: 11/07/2020
 * Time: 15:34
 */

namespace Hj\Yaml\Root;

/**
 * Class Database
 * @package Hj\Yaml\Root
 */
class Database extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'database';
    }
}