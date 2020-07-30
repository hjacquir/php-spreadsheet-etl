<?php
/**
 * User: h.jacquir
 * Date: 25/06/2020
 * Time: 12:35
 */

namespace Hj\Strategy\Admin;

use Hj\Collector\ErrorCollector;
use Hj\Error\CommandArgumentNotExistError;
use Hj\Strategy\Strategy;

/**
 * Class CheckCommandArgumentStrategy
 * @package Hj\Strategy\Admin
 */
class CheckCommandArgumentStrategy implements Strategy
{
    /**
     * @var string
     */
    private $currentArgument;

    /**
     * @var array
     */
    private $acceptedArguments = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var CommandArgumentNotExistError
     */
    private $associatedError;

    /**
     * @var bool
     */
    private $argumentIsAccepted = true;

    /**
     * CheckCommandArgumentStrategy constructor.
     * @param string $currentArgument
     * @param array $acceptedArguments
     * @param CommandArgumentNotExistError $associatedError
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        string $currentArgument,
        array $acceptedArguments,
        CommandArgumentNotExistError $associatedError,
        ErrorCollector $errorCollector
    ) {
        $this->associatedError = $associatedError;
        $this->errorCollector = $errorCollector;
        $this->currentArgument = $currentArgument;
        $this->acceptedArguments = $acceptedArguments;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return true;
    }

    public function apply()
    {
        if (false === in_array($this->currentArgument, $this->acceptedArguments)) {
            $this->argumentIsAccepted = false;
            $this->associatedError->setCurrentArgument($this->currentArgument);
            $this->associatedError->setPossibleArguments($this->acceptedArguments);
            $this->errorCollector->addError($this->associatedError);
        }
    }

    /**
     * @return bool
     */
    public function isArgumentIsAccepted(): bool
    {
        return $this->argumentIsAccepted;
    }
}