<?php
/**
 * User: h.jacquir
 * Date: 25/06/2020
 * Time: 13:11
 */

namespace Hj\Directory;

/**
 * Class NullDirectory
 * @package Hj\Directory
 */
class NullDirectory implements Directory
{
    /**
     * @param string $dirName
     * @return bool
     */
    public function hasFiles($dirName = "")
    {
        return false;
    }

    /**
     * @param string $dirName
     * @return string
     */
    public function getCurrentPoppedFileName($dirName = "")
    {
        return $dirName;
    }
}