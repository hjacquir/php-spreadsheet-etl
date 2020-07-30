<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 09:36
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\FileWithMultipleSheetsError;
use Hj\Parser\Parser;
use Hj\Strategy\Strategy;

/**
 * Class CheckIfFileHasMultipleSheet
 * @package Hj\Strategy\File
 */
class CheckIfFileHasMultipleSheet implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FileWithMultipleSheetsError
     */
    private $error;

    /**
     * CheckIfFileHasMultipleSheet constructor.
     * @param BaseDirectory $inProcessingDirectory
     * @param Parser[] $parsers
     * @param ErrorCollector $errorCollector
     * @param FileWithMultipleSheetsError $error
     */
    public function __construct(
        BaseDirectory $inProcessingDirectory,
        array $parsers,
        ErrorCollector $errorCollector,
        FileWithMultipleSheetsError $error
    ) {
        $this->inProcessingDirectory = $inProcessingDirectory;
        $this->parsers = $parsers;
        $this->errorCollector = $errorCollector;
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDirectory->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        foreach ($this->parsers as $parser) {
            if ($parser->isAppropriate()) {
                $hasMultipleSheet = $parser->checkIfFileHasMultipleSheet();

                if ($hasMultipleSheet) {
                    $this->errorCollector->addError($this->error);
                }
            }

        }
    }
}