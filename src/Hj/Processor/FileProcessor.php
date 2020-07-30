<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 13:50
 */

namespace Hj\Processor;

use Hj\Strategy\Strategy;
use Monolog\Logger;

/**
 * Class FileProcessor
 * @package Hj\Processor
 */
class FileProcessor implements Processor
{
    /**
     * @var Strategy[]
     */
    private $strategies = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * In debug mode we log the strategy being executed
     *
     * @var bool
     */
    private $debug = false;

    /**
     * FileProcessor constructor.
     * @param Logger $logger
     * @param Strategy[] $strategies
     * @param bool $debug If debug mode is setted to true we log the strategy being executed
     */
    public function __construct(
        Logger $logger,
        array $strategies,
        $debug = false
    ) {
        $this->logger = $logger;
        $this->strategies = $strategies;
        $this->debug = $debug;
    }

    public function process()
    {
        $total = count($this->getStrategies());

        foreach ($this->strategies as $key => $strategy) {
            if ($strategy->isAppropriate()) {
                if ($this->debug) {
                    $this->logger->debug("--Begin : " . get_class($strategy));
                }
                $strategy->apply();
                if ($this->debug) {
                    $this->logger->debug("--End : " . get_class($strategy));
                }

                $result = ($key/ $total) * 100;

                $this->logger->debug("Progress : " . round($result) . " %");
            }
        }
    }

    /**
     * @return Strategy[]
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }
}