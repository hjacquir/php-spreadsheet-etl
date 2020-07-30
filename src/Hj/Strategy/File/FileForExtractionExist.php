<?php
/**
 * User: h.jacquir
 * Date: 10/06/2020
 * Time: 10:19
 */

namespace Hj\Strategy\File;

use Hj\Directory\WaitingDirectory;
use Hj\Strategy\Strategy;
use Monolog\Logger;

/**
 * Class FileForExtractionExist
 * @package Hj\Strategy\File
 */
class FileForExtractionExist implements Strategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * FileForExtractionExist constructor.
     * @param WaitingDirectory $waitingDirectory
     * @param Logger $logger
     */
    public function __construct(
        WaitingDirectory $waitingDirectory,
        Logger $logger
    )
    {
        $this->waitingDirectory = $waitingDirectory;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false === $this->waitingDirectory->hasFiles();
    }

    public function apply()
    {
        $this->logger->debug("No file available for extraction.");
    }
}