<?php
/**
 * User: h.jacquir
 * Date: 17/07/2020
 * Time: 14:36
 */

namespace Hj\Cloner;

use Hj\Model\Person;

/**
 * Class PersonCloner
 * @package Hj\Cloner
 */
class PersonCloner extends AbstractCloner
{
    /**
     * @var Person
     */
    private $fieldToBeCloned;

    /**
     * PersonCloner constructor.
     * @param Person $fieldToBeCloned
     */
    public function __construct(Person $fieldToBeCloned)
    {
        $this->fieldToBeCloned = $fieldToBeCloned;
    }

    /**
     * @return object
     */
    protected function getFieldToBeCloned()
    {
        return $this->fieldToBeCloned;
    }
}