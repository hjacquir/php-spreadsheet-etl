<?php
/**
 * User: h.jacquir
 * Date: 05/05/2020
 * Time: 16:07
 */

namespace Hj\Helper;

use Exception;
use Hj\Collector\ErrorCollector;
use Hj\Directory\WaitingDirectory;
use Hj\Error\AbstractAdminError;
use Hj\Error\Database\DatabaseConnexionError;
use Hj\Error\Database\DataNotFoundInDatabaseError;
use Hj\Error\Database\DoctrinePersistenceError;
use Hj\Error\File\DirectoryNotExistError;
use Hj\Error\File\GettingSheetFromFileError;
use Hj\Error\File\LoadingFileError;
use Hj\Error\File\OnSettingValueError;
use Hj\Exception\DataNotFoundInDatabaseException;
use Hj\Exception\DirectoryNotExistException;

/**
 * Class CatchedErrorHandler
 * @package Hj\Helper
 */
class CatchedErrorHandler
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * CatchedErrorHandler constructor.
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        ErrorCollector $errorCollector
    ) {
        $this->errorCollector = $errorCollector;
    }

    /**
     * @return ErrorCollector
     */
    public function getErrorCollector(): ErrorCollector
    {
        return $this->errorCollector;
    }

    /**
     * @param DirectoryNotExistException $exception
     * @param DirectoryNotExistError $associatedError
     */
    public function handleErrorWhenDirectoryNotExist(
        DirectoryNotExistException $exception,
        DirectoryNotExistError $associatedError
    ) {
        $this->configure(
            $exception->getMessage(),
            $associatedError
        );
    }

    /**
     * @param Exception $exception
     * @param OnSettingValueError $onSettingValueError
     * @param WaitingDirectory $waitingDirectory
     */
    public function handleErrorOnSettingCellValue(
        Exception $exception,
        OnSettingValueError $onSettingValueError,
        WaitingDirectory $waitingDirectory
    ) {
        $message = "spreadsheet-etl had encountered an error when trying to define a cell value for the file  : " .
            "\n" .
            $waitingDirectory->getCurrentPoppedFileName() .
            "\n" .
            "Error message : " . $this->commonErrorMessage($exception);
        $this->configure(
            $message,
            $onSettingValueError
        );
    }

    /**
     * @param Exception $exception
     * @param GettingSheetFromFileError $gettingSheetFromFileError
     * @param WaitingDirectory $waitingDirectory
     */
    public function handleErrorOnGettingSheetFailure(
        Exception $exception,
        GettingSheetFromFileError $gettingSheetFromFileError,
        WaitingDirectory $waitingDirectory
    ) {
        $message = "Spreadsheet-etl had encountered an error when trying to load the sheets of the file : " .
            "\n" .
            $waitingDirectory->getCurrentPoppedFileName() .
            "\n" .
            "Error message : " . $this->commonErrorMessage($exception);

        $this->configure(
            $message,
            $gettingSheetFromFileError
        );
    }

    /**
     * @param Exception $exception
     * @param LoadingFileError $loadingFileError
     * @param WaitingDirectory $waitingDirectory
     */
    public function handleErrorOnFileLoadingFailure(
        Exception $exception,
        LoadingFileError $loadingFileError,
        WaitingDirectory $waitingDirectory
    ) {
        $message = "Spreadsheet-etl had encountered an error when reading the file : " .
            "\n" .
            $waitingDirectory->getCurrentPoppedFileName() .
            "\n" .
            "Error message : " . $this->commonErrorMessage($exception);
        $this->configure(
            $message,
            $loadingFileError
        );
    }

    /**
     * @param DataNotFoundInDatabaseException $exception
     * @param DataNotFoundInDatabaseError $dataNotFoundInDatabaseError
     */
    public function handleErrorWhenDataNotFoundInDatabase(
        DataNotFoundInDatabaseException $exception,
        DataNotFoundInDatabaseError $dataNotFoundInDatabaseError
    ) {
        $message = $this->commonErrorMessage($exception);
        $this->configure(
            $message,
            $dataNotFoundInDatabaseError
        );
    }

    /**
     * @param Exception $exception
     * @param DoctrinePersistenceError $doctrinePersistenceError
     */
    public function handleErrorWhenPersistenceErrorOccurred(
        Exception $exception,
        DoctrinePersistenceError $doctrinePersistenceError
    ) {
        $message = "Spreadsheet-etl had encountered an error when tryin to save the data in database : "
            . $this->commonErrorMessage($exception);
        $this->configure(
            $message,
            $doctrinePersistenceError
        );
    }

    /**
     * @param Exception $exception
     * @param DatabaseConnexionError $databaseConnexionError
     */
    public function handleErrorWhenDatabaseConnexionErrorOccurred(
        Exception $exception,
        DatabaseConnexionError $databaseConnexionError
    ) {
        $message = "Spreadsheet-etl had encountered an error when trying to connect to the database : "
            . $this->commonErrorMessage($exception);

        $this->configure(
            $message,
            $databaseConnexionError
        );
    }

    /**
     * @param Exception $exception
     * @return string
     */
    private function commonErrorMessage(Exception $exception)
    {
        // for the basic exceptions returned by the core of PHP
        // the message resulting from: $exception->getMessage () is encoded in: ISO-8859-1 because the
        // PHP files are encoded in this format. While the spreadsheet-etl project PHP files
        // are encoded in UTF-8, the message of these exceptions must therefore be encoded in UTF-8
        // because characters with accents or specials will not be decoded. However, for exceptions
        // specific to spreadsheet-etl they must not be decoded because they files
        // are already in UTF-8 hence the addition of the verification with the
        // mb_detect_encoding () function
        // @todo encapsulate this into a object
        $exceptionMessage = $exception->getMessage();

        if (mb_detect_encoding($exception->getMessage(), "UTF-8, ISO-8859-1, ISO-8859-15") !== "UTF-8") {
            $exceptionMessage = utf8_encode($exception->getMessage());
        }

        return $exceptionMessage
            . "\n\nStack trace : \n"
            . $exception->getTraceAsString();
    }

    /**
     * @param string $message
     * @param AbstractAdminError $currentError
     */
    private function configure(
        $message,
        AbstractAdminError $currentError
    ) {
        $currentErrorMessage = $currentError->getErrorMessage();

        // if the current object attribute error message is equal to the
        // default error message it is not handled
        if ($currentErrorMessage === AbstractAdminError::DEFAULT_VALUE_ERROR_MESSAGE) {
            $currentError->setErrorMessage(
                $message
            );
            $this->errorCollector->addError($currentError);
        }
    }
}