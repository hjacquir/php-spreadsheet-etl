<?php
/**
 * User: h.jacquir
 * Date: 20/08/2020
 * Time: 11:28
 */

namespace Hj\Strategy\File;

use Buuum\Ftp\FtpWrapper;
use Hj\Collector\ErrorCollector;
use Hj\Config\FtpConfig;
use Hj\Directory\WaitingDirectory;
use Hj\Error\FtpFailureDownloadFile;
use Hj\Strategy\Strategy;
use Monolog\Logger;

/**
 * Class MigrateFileStrategy
 * @package Hj\Strategy\File
 */
class MigrateFileStrategy implements Strategy
{
    /**
     * @var FtpWrapper
     */
    private $ftpWrapper;

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
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * @var FtpConfig
     */
    private $ftpConfig;

    /**
     * MigrateFileStrategy constructor.
     *
     * @param FtpConfig $ftpConfig
     * @param WaitingDirectory $waitingDirectory
     * @param FtpWrapper $ftpWrapper
     * @param Logger $logger
     * @param ErrorCollector $errorCollector
     * @param FtpFailureDownloadFile $ftpDownloadFailureError
     */
    public function __construct(
        FtpConfig $ftpConfig,
        WaitingDirectory $waitingDirectory,
        FtpWrapper $ftpWrapper,
        Logger $logger,
        ErrorCollector $errorCollector,
        FtpFailureDownloadFile $ftpDownloadFailureError
    )
    {
        $this->ftpConfig = $ftpConfig;
        $this->waitingDirectory = $waitingDirectory;
        $this->ftpWrapper = $ftpWrapper;
        $this->logger = $logger;
        $this->errorCollector = $errorCollector;
        $this->ftpDownloadFailureError = $ftpDownloadFailureError;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false === $this->errorCollector->hasError();
    }

    /**
     * @throws \Hj\Exception\KeyNotExist
     */
    public function apply()
    {
        $ftpDirectory = $this->ftpConfig
            ->getDirectory()
            ->getValue();

        $distantDirectories = $this->ftpWrapper->nlist($ftpDirectory);

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
     * @throws \Hj\Exception\KeyNotExist
     */
    private function generateLocalFileName($filePath)
    {
        $ftpDirectory = $this->ftpConfig
            ->getHost()
            ->getValue();

        return $this->waitingDirectory->getBasePath() .
            str_replace($ftpDirectory, "", $filePath);
    }
}