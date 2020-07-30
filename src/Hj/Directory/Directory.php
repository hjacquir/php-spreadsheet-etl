<?php
/**
 * User: h.jacquir
 * Date: 16/01/2020
 * Time: 17:04
 */

namespace Hj\Directory;

/**
 * Interface Directory
 * @package Hj\Directory
 */
interface Directory
{
    /**
     * @param string $dirName
     * @return bool
     */
    public function hasFiles($dirName = "");

    /**
     * @param string $dirName
     * @return string
     */
    public function getCurrentPoppedFileName($dirName = "");
}