<?php
/**
 * User: h.jacquir
 * Date: 24/07/2020
 * Time: 12:34
 */

namespace Hj\Factory;

use Hj\Config\Config;

/**
 * Interface ConfigFactory
 * @package Hj\Factory
 */
interface ConfigFactory
{
    /**
     * @param string $yamlConfigPath
     * @return Config
     */
    public function createConfig($yamlConfigPath);
}