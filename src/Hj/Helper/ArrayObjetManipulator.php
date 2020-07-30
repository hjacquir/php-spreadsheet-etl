<?php
/**
 * User: h.jacquir
 * Date: 01/04/2020
 * Time: 11:07
 */

namespace Hj\Helper;

/**
 * Class ArrayObjetManipulator
 * @package Hj\Helper
 */
class ArrayObjetManipulator
{
    /**
     * @param array $arrayOfObjectContainsAllElements
     * @param array $arrayOfObjectContainsElementToRemoveFromInitial
     * @return array The diff array of object
     */
    public function diffArray($arrayOfObjectContainsAllElements, $arrayOfObjectContainsElementToRemoveFromInitial)
    {
        $diff = [];

        foreach ($arrayOfObjectContainsAllElements as $object) {
            if (!in_array($object, $arrayOfObjectContainsElementToRemoveFromInitial)) {
               array_push($diff, $object);
            }
        }

        return $diff;
    }
}