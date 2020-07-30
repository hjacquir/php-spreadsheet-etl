<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 13:59
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\FileManipulator;
use Hj\Strategy\Strategy;

/**
 * Class CopyToFailureDirectory
 * @package Hj\Strategy\File
 */
class CopyToFailureDirectory implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $failureDirectory;

    /**
     * @var FileManipulator
     */
    private $fileManipulator;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var string
     */
    private $destination = "";

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * CopyToUnsupportedFormat constructor.
     * @param ErrorCollector $errorCollector
     * @param BaseDirectory $failureDirectory
     * @param FileManipulator $fileManipulator
     * @param BaseDirectory $inProcessingDirectory
     */
    public function __construct(
        ErrorCollector $errorCollector,
        BaseDirectory $failureDirectory,
        FileManipulator $fileManipulator,
        BaseDirectory $inProcessingDirectory
    )
    {
        $this->errorCollector = $errorCollector;
        $this->failureDirectory = $failureDirectory;
        $this->fileManipulator = $fileManipulator;
        $this->inProcessingDirectory = $inProcessingDirectory;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        // we move the file in failure directory only if user errors was thrown
        return $this->inProcessingDirectory->hasFiles()
            && true === $this->errorCollector->hasErrorForUsers();
    }

    public function apply()
    {
        $source = $this->inProcessingDirectory->getCurrentPoppedFileName();

        $destinationDirectory = $this->failureDirectory->getBasePath();

        $this->destination = $destinationDirectory .
            $this->inProcessingDirectory->getCurrentPoppedFileNameWithoutBasePath();

        $this->fileManipulator->createDirIfNotExistAndCopyFile($source, $this->destination, $destinationDirectory);
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }
}