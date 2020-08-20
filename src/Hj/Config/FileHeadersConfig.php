<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 16:53
 */

namespace Hj\Config;

use Hj\Yaml\Child\CommonMandatoryHeaders;
use Hj\Yaml\Child\OneToManyMandatoryHeaders;
use Hj\Yaml\Child\OneToOneMandatoryHeaders;
use Hj\Yaml\Child\OptionalHeaders;

/**
 * Class FileHeadersConfig
 * @package Hj\Config
 */
class FileHeadersConfig implements Config
{
    /**
     * @var OptionalHeaders
     */
    private $optionalHeadersConfig;

    /**
     * @var CommonMandatoryHeaders
     */
    private $commonMandatoryHeadersConfig;

    /**
     * MandatoryHeadersConfig constructor.
     * @param OptionalHeaders $optionalHeadersConfig
     * @param CommonMandatoryHeaders $commonMandatoryHeadersConfig
     */
    public function __construct(
        OptionalHeaders $optionalHeadersConfig,
        CommonMandatoryHeaders $commonMandatoryHeadersConfig
    )
    {
        $this->optionalHeadersConfig = $optionalHeadersConfig;
        $this->commonMandatoryHeadersConfig = $commonMandatoryHeadersConfig;
    }

    /**
     * @return OptionalHeaders
     */
    public function getOptionalHeadersConfig(): OptionalHeaders
    {
        return $this->optionalHeadersConfig;
    }

    /**
     * @return CommonMandatoryHeaders
     */
    public function getCommonMandatoryHeadersConfig(): CommonMandatoryHeaders
    {
        return $this->commonMandatoryHeadersConfig;
    }
}