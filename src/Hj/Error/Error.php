<?php
/**
 * User: h.jacquir
 * Date: 14/01/2020
 * Time: 15:21
 */

namespace Hj\Error;

/**
 * Represent an error class
 *
 * Interface Error
 * @package Hj\Error
 */
interface Error
{
    const INFO = "info";
    const CRITICAL = "critical";
    const TARGET_ADMIN = "admin";
    const TARGET_USER = "user";

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @return mixed
     */
    public function getMessage();

    /**
     * @return string
     */
    public function target();
}