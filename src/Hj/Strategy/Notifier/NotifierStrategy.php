<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 12:26
 */

namespace Hj\Strategy\Notifier;

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
     * @return array
     */
    public function getSendTo();

    /**
     * @return string
     */
    public function getBodyMessage();

    /**
     * @return string
     */
    public function getSubject();
}