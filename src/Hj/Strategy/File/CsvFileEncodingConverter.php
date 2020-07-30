<?php
/**
 * User: h.jacquir
 * Date: 10/04/2020
 * Time: 21:40
 */

namespace Hj\Strategy\File;

use Hj\Collector\ErrorCollector;
use Hj\Directory\BaseDirectory;
use Hj\Error\FileNotFoundToConvertError;
use Hj\Parser\CsvParser;
use Hj\Strategy\Strategy;

/**
 * Class CsvFileEncodingConverter
 * @package Hj\Strategy\File
 */
class CsvFileEncodingConverter implements Strategy
{
    const AUTO_DETECT_LINE_ENDINGS = 'auto_detect_line_endings';
    const ENCODING_UTF8 = 'UTF-8';
    const ENCODING_TO_DETECT = "UTF-8,ISO-8859-1,WINDOWS-1251";

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FileNotFoundToConvertError
     */
    private $associatedError;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDirectory;

    /**
     * @var CsvParser
     */
    private $csvParser;

    /**
     * @var string|bool
     */
    private $newFileName = false;

    /**
     * CsvFileEncodingConverter constructor.
     *
     * @param CsvParser $csvParser
     * @param BaseDirectory $inProcessingDirectory
     * @param ErrorCollector $errorCollector
     * @param FileNotFoundToConvertError $associatedError
     */
    public function __construct(
        CsvParser $csvParser,
        BaseDirectory $inProcessingDirectory,
        ErrorCollector $errorCollector,
        FileNotFoundToConvertError $associatedError
    )
    {
        $this->csvParser = $csvParser;
        $this->inProcessingDirectory = $inProcessingDirectory;
        $this->errorCollector = $errorCollector;
        $this->associatedError = $associatedError;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDirectory->hasFiles()
            && in_array(
                $this->inProcessingDirectory->getCurrentPoppedFileExtension(),
                $this->csvParser->getSupportedFileExtensions()
            )
            && false === $this->errorCollector->hasError();
    }

    /**
     * @param bool|string $newFileName
     */
    public function setNewFileName($newFileName): void
    {
        $this->newFileName = $newFileName;
    }

    public function apply()
    {
        $fileName = $this->inProcessingDirectory->getCurrentPoppedFileName();
        $this->convertFileToUtf8($fileName, $this->newFileName);
    }

    /**
     * @param string $fileName The file name with his path
     * @param bool $saveTo Save fixed csv file to location . If not set fixed csv file will be replaced with original csv
     *
     * @return bool
     */
    private function convertFileToUtf8($fileName, $saveTo = false)
    {
        $autoDetectLineEndings = ini_get(self::AUTO_DETECT_LINE_ENDINGS);

        ini_set(self::AUTO_DETECT_LINE_ENDINGS, true);

        if (($handle = fopen($fileName, "r")) !== false) {
            $encoding = self::ENCODING_UTF8;

            if (function_exists('mb_detect_encoding')) {
                $fileContents = file_get_contents($fileName);
                $encoding = mb_detect_encoding($fileContents, self::ENCODING_TO_DETECT);
            }

            $finaltext = "";

            while (($csv = fgetcsv($handle, 0)) !== false) {
                $csvData = [];

                foreach ($csv as $col_id => $col) {
                    array_push($csvData, html_entity_decode($this->convertDataEncoding($col, $encoding)));
                }

                $finaltext .= implode(",", $csvData) . "\n";
            }

            fclose($handle);
            ini_set(self::AUTO_DETECT_LINE_ENDINGS, $autoDetectLineEndings);

            if (!$saveTo)
                file_put_contents($fileName, $finaltext);
            else
                file_put_contents($saveTo, $finaltext);

            return true;

        } else {

            ini_set(self::AUTO_DETECT_LINE_ENDINGS, $autoDetectLineEndings);

            $this->associatedError->setFileName($fileName);
            $this->errorCollector->addError($this->associatedError);

            return false;
        }
    }

    /**
     * @param $data
     * @param $encoding
     *
     * @return bool|false|string|string[]|null
     */
    private function convertDataToUtf8($data, $encoding)
    {
        if (function_exists('iconv')) {
            $out = @iconv($encoding, 'utf-8', $data);
        } else if (function_exists('mb_convert_encoding')) {
            $out = @mb_convert_encoding($data, 'utf-8', $encoding);
        } elseif (function_exists('recode_string')) {
            $out = @recode_string($encoding . '..utf-8', $data);
        } else {
            return false;
        }

        return $out;
    }

    /**
     * @param $data
     * @param $encoding
     * @return bool|false|string|string[]|null
     */
    private function convertDataEncoding($data, $encoding)
    {
        if ($encoding === self::ENCODING_UTF8) {
            return $data;
        }

        if ($encodedData = $this->convertDataToUtf8($data, $encoding)) {
            return $encodedData;
        }

        return $data;
    }
}