<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 11:13
 */

namespace Hj\Strategy\Header;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\HeaderNotOnFirstRowError;
use Hj\Parser\Parser;
use Hj\Strategy\Strategy;

/**
 * Class OnFirstRowHeaderChecker
 * @package Hj\Strategy\Header
 */
class OnFirstRowHeaderChecker implements Strategy
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
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var HeaderNotOnFirstRowError
     */
    private $error;

    /**
     * OnFirstRowHeaderChecker constructor.
     * @param BaseDirectory $inProcessingDir
     * @param Parser[] $parsers
     * @param ErrorCollector $errorCollector
     * @param HeaderNotOnFirstRowError $error
     */
    public function __construct(
        BaseDirectory $inProcessingDir,
        array $parsers,
        ErrorCollector $errorCollector,
        HeaderNotOnFirstRowError $error
    ) {
        $this->inProcessingDir = $inProcessingDir;
        $this->parsers = $parsers;
        $this->errorCollector = $errorCollector;
        $this->error = $error;
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
                $headerIsOnFirstRow = $parser->checkIfHeaderIsOnFirstRow();

                if (false === $headerIsOnFirstRow) {
                    $this->errorCollector->addError($this->error);
                }
            }
        }
    }
}