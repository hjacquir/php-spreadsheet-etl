<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 09:52
 */

namespace Hj\Observer;

use Hj\Validator\AbstractTypeValidator;
use Hj\Validator\ValueIsArray;

/**
 * Class YamlValueValidationObserver
 * @package Hj\Observer
 */
class YamlValueIsArrayValidationObserver extends YamlValueValidationObserver
{
    /**
     * @var ValueIsArray
     */
    private $valueIsArrayValidator;

    /**
     * YamlValueValidationObserver constructor.
     * @param ValueIsArray $valueIsArrayValidator
     */
    public function __construct(ValueIsArray $valueIsArrayValidator)
    {
        $this->valueIsArrayValidator = $valueIsArrayValidator;
    }

    /**
     * @return AbstractTypeValidator
     */
    protected function getCurrentValidator()
    {
        return $this->valueIsArrayValidator;
    }
}