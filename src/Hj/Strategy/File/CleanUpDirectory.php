<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 13:59
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\Directory;
use Hj\Error\File\CleanUpDirectoryError;
use Hj\Strategy\Admin\CheckCommandArgumentStrategy;
use Hj\Strategy\Strategy;
use Monolog\Logger;
use Yalesov\FileSystemManager\FileSystemManager;

/**
 * Class CleanUpDirectory
 * @package Hj\Strategy\File
 */
class CleanUpDirectory implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $directoryToClean;

    /**
     * @var array
     */
    private $removedFiles = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var CleanUpDirectoryError
     */
    private $associatedError;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CheckCommandArgumentStrategy
     */
    private $checkCommandArgumentStrategy;

    /**
     * @var string
     */
    private $message = "The folder is empty. No file to remove.";

    /**
     * CleanUpDirectory constructor.
     * @param CheckCommandArgumentStrategy $checkCommandArgumentStrategy
     * @param Logger $logger
     * @param Directory $directoryToClean
     * @param ErrorCollector $errorCollector
     * @param CleanUpDirectoryError $associatedError
     */
    public function __construct(
        CheckCommandArgumentStrategy $checkCommandArgumentStrategy,
        Logger $logger,
        Directory $directoryToClean,
        ErrorCollector $errorCollector,
        CleanUpDirectoryError $associatedError
    ) {
        $this->checkCommandArgumentStrategy = $checkCommandArgumentStrategy;
        $this->logger = $logger;
        $this->directoryToClean = $directoryToClean;
        $this->errorCollector = $errorCollector;
        $this->associatedError = $associatedError;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->directoryToClean->hasFiles() &&
            $this->checkCommandArgumentStrategy->isArgumentIsAccepted();
    }

    public function apply()
    {
        $this->removedFiles = $this->directoryToClean->getFiles();

        $basePath = $this->directoryToClean->getBasePath();
        // remove folder with content
        $isOk = FileSystemManager::rrmdir($basePath);

        if (false === $isOk) {
            $this->associatedError->setErrorMessage("The removal of the folder {$basePath} failed.");
        } else {
            if (true === $this->fileHasRemoved()) {
                $this->message = "The folder {$basePath} has been cleaned. Files below are removed : \n\n";
            }

            $this->message .= implode("\n", $this->getRemovedFiles());

            // recreate folder empty
            mkdir($basePath);
        }
    }

    /**
     * @return bool
     */
    private function fileHasRemoved()
    {
        return count($this->getRemovedFiles()) > 0;
    }

    /**
     * @return array
     */
    public function getRemovedFiles(): array
    {
        return $this->removedFiles;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}