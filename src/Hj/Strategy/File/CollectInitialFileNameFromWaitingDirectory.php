<?php
/**
 * User: h.jacquir
 * Date: 14/05/2020
 * Time: 10:54
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\WaitingDirectory;
use Hj\Strategy\Strategy;

/**
 * Class CollectInitialFileNameFromWaitingDirectory
 * @package Hj\Strategy\File
 */
class CollectInitialFileNameFromWaitingDirectory implements Strategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * @var string
     */
    private $initialFileName = null;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * CollectInitialFileNameFromWaitingDirectory constructor.
     * @param ErrorCollector $errorCollector
     * @param WaitingDirectory $waitingDirectory
     */
    public function __construct(
        ErrorCollector $errorCollector,
        WaitingDirectory $waitingDirectory
    ) {
        $this->waitingDirectory = $waitingDirectory;
        $this->errorCollector = $errorCollector;
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
        $this->initialFileName = $this->waitingDirectory
            ->getBaseDirectory()
            ->getCurrentPoppedFileNameWithoutBasePath();
    }

    /**
     * @return string
     */
    public function getInitialFileName(): string
    {
        return $this->initialFileName;
    }
}