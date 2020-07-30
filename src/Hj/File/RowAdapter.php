<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 13:16
 */

namespace Hj\File;

use Hj\File\Field\AbstractField;

/**
 * Class RowAdapter
 * @package Hj\File
 */
class RowAdapter
{
    /**
     * @var CellAdapter[]
     */
    private $cells = [];

    /**
     * @var int
     */
    private $index;

    /**
     * @param CellAdapter $cell
     */
    public function addCell(CellAdapter $cell)
    {
        array_push($this->cells, $cell);
    }

    /**
     * @return CellAdapter[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @param CellAdapter[] $cells
     */
    public function setCells($cells)
    {
        $this->cells = $cells;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @param AbstractField $field
     * @return string|null
     */
    public function getCellNormalizedValueByField(AbstractField $field)
    {
        foreach ($this->getCells() as $cell) {
            if ($cell->getAssociatedHeader() === $field->getExpectedHeaderValue()) {
                return $cell->getNormalizedValue();
            }
        }

        return null;
    }

    /**
     * @param AbstractField $field
     * @return string|null
     */
    public function getCellInitialValueByField(AbstractField $field)
    {
        foreach ($this->getCells() as $cell) {
            if ($cell->getAssociatedHeader() === $field->getExpectedHeaderValue()) {
                return $cell->getCell()->getValue();
            }
        }

        return null;
    }
}