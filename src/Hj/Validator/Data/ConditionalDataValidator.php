<?php
/**
 * User: h.jacquir
 * Date: 23/03/2020
 * Time: 09:15
 */

namespace Hj\Validator\Data;

use Hj\Condition\Condition;
use Hj\File\CellAdapter;

/**
 * Class ConditionalDataValidator
 * @package Hj\Validator\Data
 */
class ConditionalDataValidator extends AbstractDataValidator
{
    /**
     * @var AbstractDataValidator
     */
    private $validator;

    /**
     * @var Condition
     */
    private $condition;

    /**
     * ConditionalDataValidator constructor.
     * @param AbstractDataValidator $validator
     * @param Condition $condition
     */
    public function __construct(
        AbstractDataValidator $validator,
        Condition $condition
    )
    {
        $this->validator = $validator;
        parent::__construct(
            $this->validator->getErrorCollector(),
            $this->validator->getAssociatedError(),
        );
        $this->condition = $condition;
    }

    /**
     * @param CellAdapter $cellAdapter
     *
     * @return bool
     */
    public function isValid(CellAdapter $cellAdapter)
    {
        if ($this->condition->isSatisfied()) {
            return $this->validator->isValid($cellAdapter);
        }
        return true;
    }
}