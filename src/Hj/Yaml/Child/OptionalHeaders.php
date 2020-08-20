<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:08
 */

namespace Hj\Yaml\Child;

/**
 * Class OptionalHeaders
 * @package Hj\Yaml\Child
 */
class OptionalHeaders extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'optionalHeaders';
    }
}