<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 11:04
 */

namespace Hj\Collector;

/**
 * Class AbstractCollector
 * @package Hj\Collector
 */
class CollectorIterator implements \Iterator
{
    /**
     * @var array
     */
    private $elements = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @param $element
     */
    public function addElement($element)
    {
        // avoid index beginning by O
        if ($this->index === 0) {
            $this->index = $this->index + 1;
        }

        $this->elements[$this->index] = $element;
        $this->index = $this->index + 1;
    }

    /**
     * @return bool
     */
    public function hasElements()
    {
        return count($this->elements) > 0;
    }

    /**
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->elements[$this->index];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->elements[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $firstKey = array_key_first($this->elements);
        $this->index = $firstKey;

        if (null === $this->index) {
            $this->index = 1;
        }
    }
}