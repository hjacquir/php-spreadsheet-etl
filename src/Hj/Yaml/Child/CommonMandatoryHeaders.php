<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 16:06
 */

namespace Hj\Yaml\Child;

/**
 * Class CommonMandatoryHeaders
 * @package Hj\Yaml\Child
 */
class CommonMandatoryHeaders extends AbstractChildComponent
{
    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData()
    {
        return 'commonMandatoryHeaders';
    }
}