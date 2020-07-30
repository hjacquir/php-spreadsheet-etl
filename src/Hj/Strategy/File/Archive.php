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
 * Class Archive
 * @package Hj\Strategy\File
 */
class Archive implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $archivedDir;

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
     * Archive constructor.
     *
     * @param ErrorCollector $errorCollector
     * @param BaseDirectory $archivedDir
     * @param FileManipulator $fileManipulator
     * @param BaseDirectory $inProcessingDirectory
     */
    public function __construct(
        ErrorCollector $errorCollector,
        BaseDirectory $archivedDir,
        FileManipulator $fileManipulator,
        BaseDirectory $inProcessingDirectory
    )
    {
        $this->errorCollector = $errorCollector;
        $this->archivedDir = $archivedDir;
        $this->fileManipulator = $fileManipulator;
        $this->inProcessingDirectory = $inProcessingDirectory;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDirectory->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        $source = $this->inProcessingDirectory->getCurrentPoppedFileName();

        $destinationDirectory = $this->archivedDir->getBasePath();

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