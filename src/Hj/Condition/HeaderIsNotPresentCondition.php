<?php
/**
 * User: h.jacquir
 * Date: 23/03/2020
 * Time: 09:23
 */

namespace Hj\Condition;

use Hj\File\Field\AbstractField;
use Hj\Strategy\Header\HeaderExtraction;

/**
 * Class HeaderIsNotPresentCondition
 * @package Hj\Condition
 */
class HeaderIsNotPresentCondition implements Condition
{
    /**
     * @var AbstractField
     */
    private $checkedField;

    /**
     * @var HeaderExtraction
     */
    private $headerExtractionStrategy;

    /**
     * HeaderIsNotPresentCondition constructor.
     * @param AbstractField $checkedField
     * @param HeaderExtraction $headerExtractionStrategy
     */
    public function __construct(AbstractField $checkedField, HeaderExtraction $headerExtractionStrategy)
    {
        $this->checkedField = $checkedField;
        $this->headerExtractionStrategy = $headerExtractionStrategy;
    }

    /**
     * @return bool
     */
    public function isSatisfied()
    {
        $extractedHeader = $this->headerExtractionStrategy->getExtractedHeaderValues();

        return false === in_array($this->checkedField->getExpectedHeaderValue(), $extractedHeader);
    }
}