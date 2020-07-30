<?php
/**
 * User: h.jacquir
 * Date: 27/01/2020
 * Time: 18:04
 */

namespace Hj\Strategy\Header;


use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\MandatoryHeaderMissing;
use Hj\Strategy\Strategy;
use Hj\YamlConfigLoader;

/**
 * Class MandatoryHeadersChecker
 * @package Hj\Strategy\File
 */
class MandatoryHeadersChecker implements Strategy
{
    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var MandatoryHeaderMissing
     */
    private $error;

    /**
     * @var YamlConfigLoader
     */
    private $configLoader;

    /**
     * @var HeaderExtraction
     */
    private $extractHeaderStrategy;

    /**
     * CheckCommonMandatoryHeaders constructor.
     * @param BaseDirectory $inProcessingDir
     * @param ErrorCollector $errorCollector
     * @param MandatoryHeaderMissing $error
     * @param YamlConfigLoader $configLoader
     * @param HeaderExtraction $extractHeaderStrategy
     */
    public function __construct(
        BaseDirectory $inProcessingDir,
        ErrorCollector $errorCollector,
        MandatoryHeaderMissing $error,
        YamlConfigLoader $configLoader,
        HeaderExtraction $extractHeaderStrategy
    )
    {
        $this->inProcessingDir = $inProcessingDir;
        $this->errorCollector = $errorCollector;
        $this->error = $error;
        $this->configLoader = $configLoader;
        $this->extractHeaderStrategy = $extractHeaderStrategy;
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
        $commonMandatoryHeaders = $this->configLoader->getFileCommonMandatoryHeaders();

        $extractedHeaderValues = $this->extractHeaderStrategy->getExtractedHeaderValues();

        $notFoundMandatoryHeaders = array_diff($commonMandatoryHeaders, $extractedHeaderValues);

        if (count($notFoundMandatoryHeaders) > 0) {
            $this->error->setNotFoundMandatoryHeaders($notFoundMandatoryHeaders);
            $this->errorCollector->addError($this->error);
        }
    }
}