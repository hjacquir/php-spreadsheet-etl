<?php
/**
 * User: h.jacquir
 * Date: 17/01/2020
 * Time: 11:28
 */

namespace Hj\Directory;

use Hj\Error\File\DirectoryNotExistError;
use Hj\Exception\DirectoryNotExistException;
use Hj\Helper\CatchedErrorHandler;
use Yalesov\FileSystemManager\FileSystemManager;

/**
 * Class BaseDirectory
 * @package Hj\Directory
 */
class BaseDirectory implements Directory
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    /**
     * @var DirectoryNotExistError
     */
    private $associatedError;

    /**
     * @var bool
     */
    private $directoryExist = true;

    /**
     * BaseDirectory constructor.
     *
     * @param string $basePath
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param DirectoryNotExistError $associatedError
     */
    public function __construct(
        $basePath,
        CatchedErrorHandler $catchedErrorHandler,
        DirectoryNotExistError $associatedError
    ) {
        $this->associatedError = $associatedError;
        $this->catchedErrorHandler = $catchedErrorHandler;
        $this->basePath = $basePath;

        try {
            $this->checkIfDirectoryExist();
        } catch (DirectoryNotExistException $e) {
            $this->directoryExist = false;
            $this->catchedErrorHandler
                ->handleErrorWhenDirectoryNotExist($e, $this->associatedError);
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentPoppedFileNameWithoutExtension($dirName = "")
    {
        return pathinfo($this->getCurrentPoppedFileName($dirName), PATHINFO_FILENAME);
    }

    /**
     * @param string $dirName
     * @return mixed
     */
    public function getCurrentPoppedFileExtension($dirName = "")
    {
        return pathinfo($this->getCurrentPoppedFileName($dirName), PATHINFO_EXTENSION);
    }

    /**
     * @param string $dirName
     * @return mixed
     */
    public function getCurrentPoppedFileName($dirName = "")
    {
        return $this->popOneFile($dirName);
    }

    /**
     * @param string $dirName The directory name (If directory name is empty get all files from the $basePath)
     *
     * @return array The file path as string from current and sub directory
     */
    public function getFiles($dirName = "")
    {
        $files = [];

        if (true === $this->directoryExist) {
            $files = FileSystemManager::fileIterator($this->getBasePath() . $dirName);
        }

        return $files;
    }

    /**
     * @param string $dirName
     *
     * @return bool
     */
    public function hasFiles($dirName = "")
    {
        return count($this->getFiles($dirName)) > 0;
    }

    /**
     * @return string
     */
    public function getCurrentPoppedFileNameWithoutBasePath()
    {
        return $this->getCurrentPoppedFileNameWithoutExtension() .
            "." . $this->getCurrentPoppedFileExtension();
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $dirName
     * @return mixed
     */
    private function popOneFile($dirName = "")
    {
        return current($this->getFiles($dirName));
    }

    /**
     * @throws DirectoryNotExistException
     */
    private function checkIfDirectoryExist()
    {
        if (false === is_dir($this->basePath)) {
            throw new DirectoryNotExistException(
                "The specified folder : {$this->basePath} does not exist." .
                " Please check if the folder exist or correct your .yaml config file.");
        }
    }
}