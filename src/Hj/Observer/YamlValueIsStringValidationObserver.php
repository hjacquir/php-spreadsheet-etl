<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 09:52
 */

namespace Hj\Observer;

use Hj\Validator\AbstractTypeValidator;
use Hj\Validator\ValueIsString;

/**
 * Class YamlValueIsStringValidationObserver
 * @package Hj\Observer
 */
class YamlValueIsStringValidationObserver extends YamlValueValidationObserver
{
    /**
     * @var ValueIsString
     */
    private $valueIsStringValidator;

    /**
     * YamlValueValidationObserver constructor.
     * @param ValueIsString $valueIsStringValidator
     */
    public function __construct(ValueIsString $valueIsStringValidator)
    {
        $this->valueIsStringValidator = $valueIsStringValidator;
    }

    /**
     * @return AbstractTypeValidator
     */
    protected function getCurrentValidator()
    {
        return $this->valueIsStringValidator;
    }
}