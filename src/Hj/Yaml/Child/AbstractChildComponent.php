<?php
/**
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 15:00
 */

namespace Hj\Yaml\Child;

use Hj\Exception\KeyNotExist;
use Hj\Exception\WrongTypeException;
use Hj\Observer\YamlValueValidationObserver;
use Hj\Yaml\Component;
use SplObserver;

/**
 * Class AbstractChildComponent
 * @package Hj\Yaml
 */

abstract class AbstractChildComponent implements Component,\SplSubject
{
    /**
     * @var Component
     */
    private $rootComponent;

    /**
     * AbstractChildComponent constructor.
     * @param Component $rootComponent
     * @param YamlValueValidationObserver $yamlValueValidationObserver
     * @throws KeyNotExist
     * @throws WrongTypeException
     */
    public function __construct(
        Component $rootComponent,
        YamlValueValidationObserver $yamlValueValidationObserver
    )
    {
        $this->rootComponent = $rootComponent;
        // validate
        $yamlValueValidationObserver->update($this);
    }

    /**
     * @return string
     */
    public function getYamlFilePath()
    {
        return $this->rootComponent->getYamlFilePath();
    }

    /**
     * @return array
     */
    public function getParsedValues()
    {
        return $this->rootComponent->getParsedValues();
    }

    /**
     * @return mixed
     * @throws KeyNotExist
     */
    public function getValue()
    {
        $rootComponentValue = $this->rootComponent->getValue();

        if (false === isset($rootComponentValue[$this->getKeyLabelUsedToRetrieveData()])) {
            throw new KeyNotExist("Wrong yaml file definition." .
                "The key : {$this->getKeyLabelUsedToRetrieveData()} does not exist" .
                "in the file {$this->getYamlFilePath()}. Please check your file.");
        }

        return $rootComponentValue[$this->getKeyLabelUsedToRetrieveData()];
    }

    /**
     * @return string
     */
    public function getKeyLabelUsedToDisplayMessage()
    {
        return $this->rootComponent->getKeyLabelUsedToDisplayMessage() .
            " Child key : {$this->getKeyLabelUsedToRetrieveData()}";
    }

    /**
     * Attach an SplObserver
     * @link https://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function attach(SplObserver $observer)
    {
        // TODO: Implement attach() method.
    }

    /**
     * Detach an observer
     * @link https://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function detach(SplObserver $observer)
    {
        // TODO: Implement detach() method.
    }

    /**
     * Notify an observer
     * @link https://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     */
    public function notify()
    {
        // TODO: Implement notify() method.
    }

    /**
     * @return string
     */
    public abstract function getKeyLabelUsedToRetrieveData();

}