<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 16:43
 */

namespace Hj\Strategy\Header;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\File\Field\AbstractField;
use Hj\Parser\Parser;
use Hj\Strategy\Strategy;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * Class HeaderExtraction
 * @package Hj\Strategy\Header
 */
class HeaderExtraction implements Strategy
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
     * @var Cell[]
     */
    private $extractedHeader = [];

    /**
     * @var Cell[]
     */
    private $initialExtractedHeader = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * HeaderExtraction constructor.
     * @param ErrorCollector $errorCollector
     * @param BaseDirectory $inProcessingDir
     * @param Parser[] $parsers
     */
    public function __construct(
        ErrorCollector $errorCollector,
        BaseDirectory $inProcessingDir,
        array $parsers
    )
    {
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
                $this->extractedHeader = $parser->getNormalizedHeader();
                $this->initialExtractedHeader = $parser->getInitialHeader();
            }
        }
    }

    /**
     * @return Cell[]
     */
    public function getExtractedHeader()
    {
        return $this->extractedHeader;
    }

    /**
     * @return Cell[]
     */
    public function getInitialExtractedHeader(): array
    {
        return $this->initialExtractedHeader;
    }

    /**
     * @return array
     */
    public function getExtractedHeaderValues()
    {
        $extractedHeaderValues = [];

        foreach ($this->getExtractedHeader() as $header) {
            array_push($extractedHeaderValues, $header->getValue());
        }

        return $extractedHeaderValues;
    }

    /**
     * @return array
     */
    public function getInitialExtractedHeaderValues()
    {
        $extractedHeaderValues = [];

        foreach ($this->getInitialExtractedHeader() as $header) {
            array_push($extractedHeaderValues, $header->getValue());
        }

        return $extractedHeaderValues;
    }

    /**
     * @param AbstractField $field
     *
     * @return bool
     */
    public function hasHeader(AbstractField $field)
    {
        foreach ($this->getExtractedHeaderValues() as $extractedHeaderValue) {
            if ($extractedHeaderValue === $field->getExpectedHeaderValue()) {
                return true;
            }
        }

        return false;
    }
}