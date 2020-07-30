<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 13:59
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\FileManipulator;
use Hj\Strategy\Strategy;

/**
 * Class CopyFromWaitingToInProcessing
 * @package Hj\Strategy\File
 */
class CopyFromWaitingToInProcessing implements Strategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var FileManipulator
     */
    private $fileManipulator;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * MoveInProcessing constructor.
     * @param ErrorCollector $errorCollector
     * @param WaitingDirectory $waitingDirectory
     * @param BaseDirectory $inProcessingDir
     * @param FileManipulator $fileManipulator
     */
    public function __construct(
        ErrorCollector $errorCollector,
        WaitingDirectory $waitingDirectory,
        BaseDirectory $inProcessingDir,
        FileManipulator $fileManipulator
    ) {
        $this->errorCollector = $errorCollector;
        $this->waitingDirectory = $waitingDirectory;
        $this->inProcessingDir = $inProcessingDir;
        $this->fileManipulator = $fileManipulator;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->waitingDirectory->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        $source = $this->waitingDirectory->getCurrentPoppedFileName();
        $destination = $this->inProcessingDir->getBasePath() . $this->waitingDirectory->generateNewCurrentFileName();
        // copy in processing
        $this->fileManipulator->createDirIfNotExistAndCopyFile($source, $destination);
    }
}