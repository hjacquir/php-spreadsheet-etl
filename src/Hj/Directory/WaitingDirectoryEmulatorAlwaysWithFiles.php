<?php
/**
 * User: h.jacquir
 * Date: 08/04/2020
 * Time: 16:59
 */

namespace Hj\Directory;

/**
 * Class WaitingDirectoryEmulatorAlwaysWithFiles
 * @package Hj\Directory
 */
class WaitingDirectoryEmulatorAlwaysWithFiles implements Directory
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * WaitingDirectoryEmulatorAlwaysWithFiles constructor.
     * @param BaseDirectory $inProcessingDirectory
     */
    public function __construct(BaseDirectory $inProcessingDirectory)
    {
        $this->inProcessingDirectory = $inProcessingDirectory;
    }


    public function hasFiles($dirName = "")
    {
        return true;
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