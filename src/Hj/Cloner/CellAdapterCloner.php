<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 14:32
 */

namespace Hj\Cloner;

use Hj\File\CellAdapter;

/**
 * Class CellAdapterCloner
 * @package Hj\Cloner
 */
class CellAdapterCloner extends AbstractCloner
{
    /**
     * @var CellAdapter
     */
    private $cellAdapter;

    /**
     * CellAdapterCloner constructor.
     * @param CellAdapter $cellAdapter
     */
    public function __construct(CellAdapter $cellAdapter)
    {
        $this->cellAdapter = $cellAdapter;
    }

    /**
     * @return object
     */
    protected function getFieldToBeCloned()
    {
        return $this->cellAdapter;
    }
}