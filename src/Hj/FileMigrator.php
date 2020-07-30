<?php
/**
 * User: h.jacquir
 * Date: 21/01/2020
 * Time: 14:20
 */

namespace Hj;

use Buuum\Ftp\FtpWrapper;
use Hj\Collector\ErrorCollector;
use Hj\Error\FtpFailureDownloadFile;
use Monolog\Logger;

/**
 * Class FileMigrator
 * @package Hj
 */
class FileMigrator
{
    /**
     * @var FtpWrapper
     */
    private $ftpWrapper;


    /**
     * @var YamlConfigLoader
     */
    private $configLoader;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FtpFailureDownloadFile
     */
    private $ftpDownloadFailureError;

    /**
     * @var array
     */
    private $filesMigrated = [];

    /**
     * FileMigrator constructor.
     * @param FtpWrapper $ftpWrapper
     * @param YamlConfigLoader $configLoader
     * @param Logger $logger
     * @param ErrorCollector $errorCollector
     * @param FtpFailureDownloadFile $ftpDownloadFailureError
     */
    public function __construct(
        FtpWrapper $ftpWrapper,
        YamlConfigLoader $configLoader,
        Logger $logger,
        ErrorCollector $errorCollector,
        FtpFailureDownloadFile $ftpDownloadFailureError
    ) {
        $this->ftpWrapper = $ftpWrapper;
        $this->configLoader = $configLoader;
        $this->logger = $logger;
        $this->errorCollector = $errorCollector;
        $this->ftpDownloadFailureError = $ftpDownloadFailureError;
    }

    public function migrate()
    {
        $distantDirectories = $this->ftpWrapper->nlist($this->configLoader->getFtpDirectory());

        foreach ($distantDirectories as $distantDirectory) {
            $currentDirFiles = $this->ftpWrapper->nlist($distantDirectory);

            if ($this->dirIsNotEmpty($currentDirFiles)) {
                foreach ($currentDirFiles as $file) {
                    $localFilePath = $this->generateLocalFileName($file);
                    $remoteFilePath = $file;
                    $isDownloaded = $this->ftpWrapper->get($localFilePath, $remoteFilePath);

                    if (false === $isDownloaded) {
                        $this->ftpDownloadFailureError->setDirName($remoteFilePath);
                        $this->errorCollector->addError($this->ftpDownloadFailureError);
                    } else {
                        $this->ftpWrapper->delete($remoteFilePath);

                        // @todo encapsulate this
                        if (mb_detect_encoding($remoteFilePath, "UTF-8, ISO-8859-1, ISO-8859-15") !== "UTF-8") {
                            $remoteFilePath = utf8_encode($remoteFilePath);
                        }

                        array_push($this->filesMigrated, "The file : {$remoteFilePath} had been migrated successfully.");
                    }
                }
            }
        }
        $this->logger->info($this->getMigrationMessage());
    }

    /**
     * @return string
     */
    public function getMigrationMessage(): string
    {
        if (count($this->filesMigrated) > 0) {
            return implode("\n", $this->filesMigrated);
        }

        return "No file to migrate today.";
    }

    /**
     * @param array $dir
     * @return bool
     */
    private function dirIsNotEmpty($dir)
    {
        return count($dir) > 0;
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function generateLocalFileName($filePath)
    {
        return $this->configLoader->getWaitingFilePath() . str_replace($this->configLoader->getFtpDirectory(), "",
                $filePath);
    }
}