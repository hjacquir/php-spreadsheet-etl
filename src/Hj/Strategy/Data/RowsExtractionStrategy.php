<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 11:24
 */

namespace Hj\Strategy\Data;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Parser\Parser;
use Hj\Strategy\Strategy;

/**
 * Class RowsExtractionStrategy
 * @package Hj\Strategy\Data
 */
class RowsExtractionStrategy implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var array
     */
    private $extractedRows = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * RowsExtractionStrategy constructor.
     * @param ErrorCollector $errorCollector
     * @param BaseDirectory $inProcessingDir
     * @param Parser[] $parsers
     */
    public function __construct(
        ErrorCollector $errorCollector,
        BaseDirectory $inProcessingDir,
        array $parsers
    ) {
        $this->errorCollector = $errorCollector;
        $this->inProcessingDir = $inProcessingDir;
        $this->parsers = $parsers;
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
        foreach ($this->parsers as $parser) {
            if ($parser->isAppropriate()) {
                $this->extractedRows = $parser->getCells();
            }
        }
    }

    /**
     * @return array
     */
    public function getExtractedRows()
    {
        return $this->extractedRows;
    }
}