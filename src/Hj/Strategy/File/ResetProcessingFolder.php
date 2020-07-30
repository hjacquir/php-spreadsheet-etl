<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 14:33
 */

namespace Hj\Strategy\File;

use Hj\Directory\BaseDirectory;
use Hj\Strategy\Strategy;
use Yalesov\FileSystemManager\FileSystemManager;

/**
 * Class ResetProcessingFolder
 * @package Hj\Strategy\File
 */
class ResetProcessingFolder implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * ResetProcessingFolder constructor.
     * @param BaseDirectory $inProcessingDir
     */
    public function __construct(
        BaseDirectory $inProcessingDir
    )
    {
        $this->inProcessingDir = $inProcessingDir;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDir->hasFiles();
    }

    public function apply()
    {
        $basePath = $this->inProcessingDir->getBasePath();
        // remove folder with content
        FileSystemManager::rrmdir($basePath);
        // recreate folder empty
        mkdir($basePath);
    }
}