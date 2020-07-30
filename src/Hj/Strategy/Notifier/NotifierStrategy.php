<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 12:26
 */

namespace Hj\Strategy\Notifier;

use Hj\YamlConfigLoader;

/**
 * Interface NotifierStrategy
 * @package Hj\Strategy\Notifier
 */
interface NotifierStrategy
{
    /**
     * @return bool
     */
    public function isAppropriate();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @param YamlConfigLoader $configLoader
     * @return array
     */
    public function getSendTo(YamlConfigLoader $configLoader);

    /**
     * @return string
     */
    public function getBodyMessage();

    /**
     * @return string
     */
    public function getSubject();
}