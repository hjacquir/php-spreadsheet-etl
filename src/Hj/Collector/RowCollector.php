<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 11:03
 */

namespace Hj\Collector;

use Hj\File\RowAdapter;

class RowCollector
{
    /**
     * @var CollectorIterator
     */
    private $collectorIterator;

    /**
     * CellCollector constructor.
     * @param CollectorIterator $collectorIterator
     */
    public function __construct(CollectorIterator $collectorIterator)
    {
        $this->collectorIterator = $collectorIterator;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->collectorIterator->key();
    }

    /**
     * @param RowAdapter $row
     */
    public function addRow(RowAdapter $row)
    {
        $this->collectorIterator->addElement($row);
    }

    /**
     * @return RowAdapter[]
     */
    public function current()
    {
        return $this->collectorIterator->current();
    }

    public function next()
    {
        $this->collectorIterator->next();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->collectorIterator->valid();
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->collectorIterator->getElements();
    }

    public function rewind()
    {
        $this->collectorIterator->rewind();
    }
}