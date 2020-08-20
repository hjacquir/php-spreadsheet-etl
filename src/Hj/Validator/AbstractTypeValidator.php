<?php
/**
 * Created by PhpStorm.
 * User: h.jacquir
 * Date: 20/07/2020
 * Time: 11:49
 */

namespace Hj\Validator;

/**
 * Class AbstractTypeValidator
 * @package Hj\Validator
 */
abstract class AbstractTypeValidator implements Validator
{
    /**
     * @return string
     */
    public abstract function getExpectedType();
}