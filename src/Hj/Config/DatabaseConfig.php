<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 16:39
 */

namespace Hj\Config;

use Hj\Yaml\Child\Url;

/**
 * Class DatabaseConfig
 * @package Hj\Config
 */
class DatabaseConfig implements Config
{
    /**
     * @var Url
     */
    private Url $url;

    /**
     * DatabaseConfig constructor.
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }
}