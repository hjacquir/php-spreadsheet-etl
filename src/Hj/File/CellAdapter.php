<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 11:01
 */

namespace Hj\File;

use Hj\File\Field\AbstractField;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * Class CellAdapter
 * @package Hj
 */
class CellAdapter
{
    /**
     * @var int
     */
    private $rowIndex;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var Cell
     */
    private $cell;

    /**
     * @var string
     */
    private $associatedHeader;

    /**
     * @var RowAdapter
     */
    private $rowAdapter;

    /**
     * Other cell adapter on the same row. Friends of this
     *
     * @var CellAdapter[]
     */
    private $friends = [];

    /**
     * @var string
     */
    private $normalizedValue;

    /**
     * @var string
     */
    private $initialAssociatedHeader;

    /**
     * @return string
     */
    public function getInitialAssociatedHeader(): string
    {
        return $this->initialAssociatedHeader;
    }

    /**
     * @param string $initialAssociatedHeader
     */
    public function setInitialAssociatedHeader(string $initialAssociatedHeader): void
    {
        $this->initialAssociatedHeader = $initialAssociatedHeader;
    }

    /**
     * @return string|null
     */
    public function getNormalizedValue()
    {
        return $this->normalizedValue;
    }

    /**
     * @param string|null $normalizedValue
     */
    public function setNormalizedValue($normalizedValue): void
    {
        $this->normalizedValue = $normalizedValue;
    }

    /**
     * @return RowAdapter
     */
    public function getRowAdapter(): RowAdapter
    {
        return $this->rowAdapter;
    }

    /**
     * @param RowAdapter $rowAdapter
     */
    public function setRowAdapter(RowAdapter $rowAdapter): void
    {
        $this->rowAdapter = $rowAdapter;
    }

    /**
     * @return CellAdapter[]
     */
    public function getFriends(): array
    {
        foreach ($this->rowAdapter->getCells() as $cell) {
            if ($cell !== $this) {
                array_push($this->friends, $cell);
            }
        }

        return $this->friends;
    }

    /**
     * @return string
     */
    public function getAssociatedHeader()
    {
        return $this->associatedHeader;
    }

    /**
     * @param string $associatedHeader
     */
    public function setAssociatedHeader($associatedHeader)
    {
        $this->associatedHeader = $associatedHeader;
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * @return Cell
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * @param Cell $cell
     */
    public function setCell(Cell $cell)
    {
        $this->cell = $cell;
    }

    /**
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * @param int $rowIndex
     */
    public function setRowIndex($rowIndex)
    {
        $this->rowIndex = $rowIndex;
    }

    /**
     * @param AbstractField $field
     * @return CellAdapter|mixed|null
     */
    public function getFriendBasedOnField(AbstractField $field)
    {
        foreach ($this->getFriends() as $friend) {
            if ($friend->getAssociatedHeader() === $field->getExpectedHeaderValue()) {
                return $friend;
            }
        }

        return null;
    }
}