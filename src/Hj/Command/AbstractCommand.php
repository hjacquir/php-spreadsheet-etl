<?php
/**
 * User: h.jacquir
 * Date: 12/05/2020
 * Time: 15:57
 */

namespace Hj\Command;

use Hj\Collector\ErrorCollector;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 * @package Hj\Command
 */
abstract class AbstractCommand extends Command
{
    /**
     * @return ErrorCollector
     */
    public abstract function getErrorCollector();
}