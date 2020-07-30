<?php
/**
 * User: h.jacquir
 * Date: 14/01/2020
 * Time: 11:07
 */

namespace Hj\Collector;

use Hj\Error\Error;

/**
 * Collects all error
 *
 * Class ErrorCollector
 * @package Hj\Collector
 */
class ErrorCollector
{
    /**
     * @var CollectorIterator
     */
    private $collectorIterator;

    /**
     * ErrorCollector constructor.
     * @param CollectorIterator $collectorIterator
     */
    public function __construct(CollectorIterator $collectorIterator)
    {
        $this->collectorIterator = $collectorIterator;
    }

    /**
     * @return bool
     */
    public function hasErrorForAdmins()
    {
        return count($this->getAllAdminErrors()) > 0;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return count($this->collectorIterator->getElements()) > 0;
    }

    /**
     * @return bool
     */
    public function hasErrorForUsers()
    {
        return count($this->getAllUserErrors()) > 0;
    }

    /**
     * @return array
     */
    public function getAllAdminErrors()
    {
        return $this->getAllErrorsByTarget(Error::TARGET_ADMIN);
    }

    /**
     * @return array
     */
    public function getAllUserErrors()
    {
        return $this->getAllErrorsByTarget(Error::TARGET_USER);
    }

    /**
     * Add an error to the collector
     *
     * @param Error $error
     */
    public function addError(Error $error)
    {
        $this->collectorIterator->addElement($error);
    }

    /**
     * @param string $target
     * @return array
     */
    private function getAllErrorsByTarget($target)
    {
        $errors = [];
        $this->collectorIterator->rewind();

        while ($this->collectorIterator->valid()) {
            $current = $this->collectorIterator->current();

            if ($current->target() === $target) {
                array_push($errors, $current);
            }
            $this->collectorIterator->next();
        }

        return $errors;
    }
}