<?php
/**
 * User: h.jacquir
 * Date: 16/01/2020
 * Time: 16:48
 */

namespace Hj\Directory;

/**
 * Class WaitingDirectory
 * @package Hj\Directory
 */
class WaitingDirectory implements Directory
{
    /**
     * @var BaseDirectory
     */
    private $baseDirectory;

    /**
     * WaitingDirectory constructor.
     *
     * @param BaseDirectory $baseDirectory
     */
    public function __construct(
        BaseDirectory $baseDirectory
    )
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * @return BaseDirectory
     */
    public function getBaseDirectory(): BaseDirectory
    {
        return $this->baseDirectory;
    }

    /**
     * @param BaseDirectory $baseDirectory
     */
    public function setBaseDirectory(BaseDirectory $baseDirectory): void
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * @param string $dirName
     * @return bool
     */
    public function hasFiles($dirName = "")
    {
        return $this->baseDirectory->hasFiles($dirName);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->baseDirectory->getBasePath();
    }

    /**
     * @param string $dirName
     * @return string
     */
    public function getCurrentPoppedFileName($dirName = "")
    {
        return $this->baseDirectory->getCurrentPoppedFileName($dirName);
    }

    /**
     * @return string
     */
    public function generateNewCurrentFileName()
    {
        $timestamp = (string)time();

        return $this->baseDirectory->getCurrentPoppedFileNameWithoutExtension()  .
            "__" .
            $timestamp .
            "." .
            $this->baseDirectory->getCurrentPoppedFileExtension();
    }

    /**
     * @param string $dirName
     * @return string
     */
    public function getCurrentPoppedFileExtension($dirName = "")
    {
        return $this->baseDirectory->getCurrentPoppedFileExtension($dirName);
    }
}