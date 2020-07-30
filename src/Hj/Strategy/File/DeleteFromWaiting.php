<?php
/**
 * User: h.jacquir
 * Date: 20/01/2020
 * Time: 15:18
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Directory\WaitingDirectory;
use Hj\Strategy\Strategy;

/**
 * Class DeleteFromWaiting
 * @package Hj\Strategy\File
 */
class DeleteFromWaiting implements Strategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDir;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * DeleteFromWaiting constructor.
     *
     * @param ErrorCollector $errorCollector
     * @param WaitingDirectory $waitingDir
     * @param BaseDirectory $inProcessingDirectory
     */
    public function __construct(
        ErrorCollector $errorCollector,
        WaitingDirectory $waitingDir,
        BaseDirectory $inProcessingDirectory
    )
    {
        $this->errorCollector = $errorCollector;
        $this->waitingDir = $waitingDir;
        $this->inProcessingDirectory = $inProcessingDirectory;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        // we delete the file only if no error requiring administrator intervention occurs
        return $this->inProcessingDirectory->hasFiles()
            && false === $this->errorCollector->hasErrorForAdmins();
    }

    public function apply()
    {
        $source = $this->waitingDir->getCurrentPoppedFileName();

        unlink($source);
    }
}