<?php
/**
 * User: h.jacquir
 * Date: 27/07/2020
 * Time: 15:46
 */

namespace Hj\Config;

use Hj\Yaml\Child\Archived;
use Hj\Yaml\Child\Failure;
use Hj\Yaml\Child\InProcessing;
use Hj\Yaml\Child\Waiting;

/**
 * Class FilePathConfig
 * @package Hj\Config
 */
class FilePathConfig implements Config
{
    /**
     * @var Waiting
     */
    private $waitingDir;

    /**
     * @var InProcessing
     */
    private $inProcessing;

    /**
     * @var Archived
     */
    private $archived;

    /**
     * @var Failure
     */
    private $failure;

    /**
     * FilePathConfig constructor.
     * @param Waiting $waitingDir
     * @param InProcessing $inProcessing
     * @param Archived $archived
     * @param Failure $failure
     */
    public function __construct(
        Waiting $waitingDir,
        InProcessing $inProcessing,
        Archived $archived,
        Failure $failure
    )
    {
        $this->waitingDir = $waitingDir;
        $this->inProcessing = $inProcessing;
        $this->archived = $archived;
        $this->failure = $failure;
    }

    /**
     * @return Waiting
     */
    public function getWaitingDir(): Waiting
    {
        return $this->waitingDir;
    }

    /**
     * @return InProcessing
     */
    public function getInProcessing(): InProcessing
    {
        return $this->inProcessing;
    }

    /**
     * @return Archived
     */
    public function getArchived(): Archived
    {
        return $this->archived;
    }

    /**
     * @return Failure
     */
    public function getFailure(): Failure
    {
        return $this->failure;
    }
}