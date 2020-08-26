<?php
/**
 * User: h.jacquir
 * Date: 11/02/2020
 * Time: 09:19
 */

namespace Hj\Strategy\Header;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\DuplicateHeaderError;
use Hj\Strategy\Strategy;

/**
 * Class HeaderUnicityChecker
 * @package Hj\Strategy\Header
 */
class HeaderUnicityChecker implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var HeaderExtraction
     */
    private $extractHeaderStrategy;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var DuplicateHeaderError
     */
    private $duplicateHeaderError;

    /**
     * HeaderUnicityChecker constructor.
     * @param BaseDirectory $inProcessingDir
     * @param HeaderExtraction $extractHeaderStrategy
     * @param ErrorCollector $errorCollector
     * @param DuplicateHeaderError $duplicateHeaderError
     */
    public function __construct(
        BaseDirectory $inProcessingDir,
        HeaderExtraction $extractHeaderStrategy,
        ErrorCollector $errorCollector,
        DuplicateHeaderError $duplicateHeaderError
    )
    {
        $this->inProcessingDir = $inProcessingDir;
        $this->extractHeaderStrategy = $extractHeaderStrategy;
        $this->errorCollector = $errorCollector;
        $this->duplicateHeaderError = $duplicateHeaderError;
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
        $extractedHeader = $this->extractHeaderStrategy->getExtractedHeader();

        $values = [];
        $duplicates = [];

        foreach ($extractedHeader as $key => $header) {
            if (in_array($header->getValue(), $values, true)) {
                $duplicates[$key] = $header;
            }
            array_push($values, $header->getValue());
        }

        if (count($duplicates) > 0) {
            $this->duplicateHeaderError->setDuplicatedHeaders($duplicates);
            $this->errorCollector->addError($this->duplicateHeaderError);
        }
    }
}