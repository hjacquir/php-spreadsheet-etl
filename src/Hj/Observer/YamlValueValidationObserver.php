<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 10:17
 */

namespace Hj\Observer;

use Hj\Exception\WrongTypeException;
use Hj\Validator\AbstractTypeValidator;
use Hj\Yaml\Component;
use SplSubject;

/**
 * Class YamlValueValidationObserver
 * @package Hj\Observer
 */
abstract class YamlValueValidationObserver implements \SplObserver
{
    /**
     * @param SplSubject $subject
     * @throws \Hj\Exception\KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function update(SplSubject $subject)
    {
        /** @var Component $component */
        $component = $subject;
        $currentValue = $component->getValue();

        if (false === $this->getCurrentValidator()->valid($currentValue)) {
            throw new WrongTypeException("The value type for the key : " .
                "{$component->getKeyLabelUsedToDisplayMessage()}" .
                " on the yaml file : {$component->getYamlFilePath()}  is not as expected." .
                " The expected type is : {$this->getCurrentValidator()->getExpectedType()}");
        }
    }

    /**
     * @return AbstractTypeValidator
     */
    protected abstract function getCurrentValidator();
}