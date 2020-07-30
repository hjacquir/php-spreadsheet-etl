<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 14:30
 */

namespace Hj\Cloner;

use Hj\File\RowAdapter;

/**
 * Class RowAdapterCloner
 * @package Hj\Cloner
 */
class RowAdapterCloner extends AbstractCloner
{
    /**
     * @var RowAdapter
     */
    private $rowAdapter;

    /**
     * RowAdapterCloner constructor.
     * @param RowAdapter $rowAdapter
     */
    public function __construct(RowAdapter $rowAdapter)
    {
        $this->rowAdapter = $rowAdapter;
    }


    /**
     * @return object
     */
    protected function getFieldToBeCloned()
    {
        return $this->rowAdapter;
    }
}