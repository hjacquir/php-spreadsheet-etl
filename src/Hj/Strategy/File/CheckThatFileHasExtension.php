<?php
/**
 * User: h.jacquir
 * Date: 20/08/2020
 * Time: 15:57
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\WaitingDirectory;
use Hj\Error\File\FileWithoutExtensionError;
use Hj\Strategy\Strategy;

/**
 * Class CheckThatFileHasExtension
 * @package Hj\Strategy\File
 */
class CheckThatFileHasExtension implements Strategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FileWithoutExtensionError
     */
    private $associatedError;

    /**
     * CheckThatFileHasExtension constructor.
     * @param WaitingDirectory $waitingDirectory
     * @param ErrorCollector $errorCollector
     * @param FileWithoutExtensionError $associatedError
     */
    public function __construct(
        WaitingDirectory $waitingDirectory,
        ErrorCollector $errorCollector,
        FileWithoutExtensionError $associatedError
    ) {
        $this->waitingDirectory = $waitingDirectory;
        $this->errorCollector = $errorCollector;
        $this->associatedError = $associatedError;
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
        $currentExtension = $this->waitingDirectory->getCurrentPoppedFileExtension();

        if ("" === $currentExtension) {
          $this->associatedError->setFilePath($this->waitingDirectory->getCurrentPoppedFileName());
          $this->errorCollector->addError($this->associatedError);
        }
    }
}