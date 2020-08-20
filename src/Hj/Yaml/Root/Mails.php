<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:52
 */

namespace Hj\Yaml\Root;

/**
 * Class Mails
 * @package Hj\Yaml\Root
 */
class Mails extends AbstractRootComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'mails';
    }
}