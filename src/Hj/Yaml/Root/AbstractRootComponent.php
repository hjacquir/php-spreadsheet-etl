<?php
/**
 * Created by PhpStorm.
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 14:48
 */

namespace Hj\Yaml\Root;

use Hj\Exception\KeyNotExist;
use Hj\Observer\YamlValueValidationObserver;
use Hj\Yaml\Component;
use SplObserver;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AbstractRootComponent
 * @package Hj\Yaml
 */
abstract class AbstractRootComponent implements Component, \SplSubject
{
    /**
     * @var string
     */
    private $yamlFilePath;

    /**
     * @var array
     */
    private $parsedValues = [];

    /**
     * AbstractRootComponent constructor.
     * @param $yamlFilePath
     * @param YamlValueValidationObserver $yamlValueValidationObserver
     * @throws KeyNotExist
     * @throws \Hj\Exception\WrongTypeException
     */
    public function __construct(
        $yamlFilePath,
        YamlValueValidationObserver $yamlValueValidationObserver
    )
    {
        $this->yamlFilePath = $yamlFilePath;
        $this->parsedValues = Yaml::parseFile($this->yamlFilePath);
        // validate that value is in right format
        $yamlValueValidationObserver->update($this);
    }

    /**
     * @return string
     */
    public function getYamlFilePath()
    {
        return $this->yamlFilePath;
    }

    /**
     * @return array
     */
    public function getParsedValues()
    {
        return $this->parsedValues;
    }

    /**
     * @return mixed
     * @throws KeyNotExist
     */
    public function getValue()
    {
        if (false === isset($this->parsedValues[$this->getKeyLabelUsedToRetrieveData()])) {
            throw new KeyNotExist("Wrong yaml file definition. The root key : {$this->getKeyLabelUsedToRetrieveData()} " .
                "does not exist in the file : {$this->getYamlFilePath()}. Please check your yaml file.");
        }

        return $this->parsedValues[$this->getKeyLabelUsedToRetrieveData()];
    }

    /**
     * @return string
     */
    public function getKeyLabelUsedToDisplayMessage()
    {
        return " Root key : {$this->getKeyLabelUsedToRetrieveData()}";
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