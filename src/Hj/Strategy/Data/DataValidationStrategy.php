<?php
/**
 * User: h.jacquir
 * Date: 18/02/2020
 * Time: 15:44
 */

namespace Hj\Strategy\Data;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\File\CellAdapter;
use Hj\File\Field\Field;
use Hj\File\RowAdapter;
use Hj\Strategy\Strategy;

/**
 * Class DataValidationStrategy
 * @package Hj\Strategy\Data
 */
class DataValidationStrategy implements Strategy
{
    /**
     * @var CollectRowAdapterStrategy
     */
    private $collectRowAdapterStrategy;

    /**
     * @var RowAdapter
     */
    private $currentRowAdapter;

    /**
     * @var CellAdapter[]
     */
    private $cellAdapters;

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * DataValidationStrategy constructor.
     * @param ErrorCollector $errorCollector
     * @param CollectRowAdapterStrategy $collectRowAdapterStrategy
     * @param Field[] $fields
     * @param BaseDirectory $inProcessingDir
     */
    public function __construct(
        ErrorCollector $errorCollector,
        CollectRowAdapterStrategy $collectRowAdapterStrategy,
        array $fields,
        BaseDirectory $inProcessingDir
    ) {
        $this->errorCollector = $errorCollector;
        $this->collectRowAdapterStrategy = $collectRowAdapterStrategy;
        $this->fields = $fields;
        $this->inProcessingDir = $inProcessingDir;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDir->hasFiles()
            && false === $this->errorCollector->hasError();
    }

    public function apply()
    {
        $collector = $this->collectRowAdapterStrategy->getRowCollector();
        while ($collector->valid()) {
            $this->currentRowAdapter = $collector->current();
            $this->cellAdapters = $this->currentRowAdapter->getCells();

            foreach ($this->cellAdapters as $cellAdapter) {
                foreach ($this->fields as $field) {
                    if ($field->getExpectedHeaderValue() === $cellAdapter->getAssociatedHeader()) {
                        $field->validValue($cellAdapter);
                    }
                }
            }

            $collector->next();
        }

        // add error
        foreach ($this->fields as $field) {
            foreach ($field->getValidator() as $validator) {
                $validator->logError();
            }
        }
    }
}