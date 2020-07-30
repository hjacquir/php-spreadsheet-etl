<?php
/**
 * User: h.jacquir
 * Date: 16/04/2020
 * Time: 14:06
 */

namespace Hj\Cloner;

/**
 * Class AbstractCloner
 *
 * @package Hj\Cloner
 */
abstract class AbstractCloner implements Cloner
{
    /**
     * @return object
     *
     * @throws \ReflectionException
     */
    public function replicate()
    {
        $currentClassName = get_class($this->getFieldToBeCloned());
        $reflectionClass = new \ReflectionClass($currentClassName);

        return $reflectionClass->newInstance();
    }

    /**
     * @return object
     */
    protected abstract function getFieldToBeCloned();
}