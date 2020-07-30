<?php
/**
 * User: h.jacquir
 * Date: 16/01/2020
 * Time: 17:03
 */

namespace Hj;

/**
 * Class FileManipulator
 * @package Hj
 */
class FileManipulator
{
    /**
     * @param string $src The source file
     * @param string $dest The destination file
     * @param string|null $destDir The destination dir (will be created if no exist)
     */
    public function createDirIfNotExistAndCopyFile($src, $dest, $destDir = null)
    {
        if (!is_dir($destDir) && null !== $destDir) {
            //Directory does not exist, so lets create it.
            mkdir($destDir, 0755);
        }
        copy($src, $dest);
    }
}