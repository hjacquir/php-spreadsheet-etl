<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 15:59
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\FileExtensionError;
use Hj\Strategy\Strategy;

/**
 * Class CheckFileExtension
 * @package Hj\Strategy\File
 */
class CheckFileExtension implements Strategy
{
    const SUPPORTED_EXTENSIONS = [
        'csv',
        'CSV',
        'xls',
        'XLS',
        'xlsx',
        'XLSX',
        'ods',
        'ODS',
    ];
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FileExtensionError
     */
    private $fileExtensionError;

    /**
     * CheckFileExtension constructor.
     * @param BaseDirectory $inProcessingDir
     * @param ErrorCollector $errorCollector
     * @param FileExtensionError $fileExtensionError
     */
    public function __construct(
        BaseDirectory $inProcessingDir,
        ErrorCollector $errorCollector,
        FileExtensionError $fileExtensionError
    )
    {
        $this->inProcessingDir = $inProcessingDir;
        $this->errorCollector = $errorCollector;
        $this->fileExtensionError = $fileExtensionError;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDir->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        $currentExtension = $this->inProcessingDir->getCurrentPoppedFileExtension();

        if (false === $this->isSupported($currentExtension)) {
            $this->errorCollector->addError($this->fileExtensionError);
        }
    }

    /**
     * @param string $currentExtension
     *
     * @return bool
     */
    private function isSupported($currentExtension)
    {
            if (in_array($currentExtension, self::SUPPORTED_EXTENSIONS)) {
                return true;
            }

        return false;
    }
}