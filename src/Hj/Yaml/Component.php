<?php
/**
 * User: h.jacquir
 * Date: 11/07/2020
 * Time: 15:46
 */

namespace Hj\Yaml;

use Hj\Exception\KeyNotExist;

/**
 * Interface Component
 * @package Hj\Yaml
 */
interface Component
{
    /**
     * @return string
     */
    public function getYamlFilePath();

    /**
     * @return array
     */
    public function getParsedValues();

    /**
     * @return mixed
     * @throws KeyNotExist
     */
    public function getValue();

    /**
     * @return string
     */
    public function getKeyLabelUsedToDisplayMessage();

    /**
     * @return string
     */
    public function getKeyLabelUsedToRetrieveData();
}