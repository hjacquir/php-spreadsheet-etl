<?php
/**
 * User: h.jacquir
 * Date: 13/05/2020
 * Time: 22:01
 */

namespace Hj\Tests\Functional\Command;

use Hj\Command\MigrateFileFromDistant;
use Hj\Error\Error;
use Hj\Error\FtpFailureConnexion;
use Hj\Helper\Tests\AbstractFunctionalTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class MigrateFileFromDistantTest
 * @package Hj\Tests\Functional\Command
 * @covers \Hj\Command\MigrateFileFromDistant
 */
class MigrateFileFromDistantTest extends AbstractFunctionalTestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @return string
     */
    protected function getCurrentContextualDir()
    {
        return __DIR__;
    }

    /**
     * @return array
     */
    public function provideDataForTestsWithFailure()
    {
        return [
            // ftp connection error
            'ftpConnectionError' => [
                "ftpConnectionError.yaml",
                FtpFailureConnexion::class,
                Error::TARGET_ADMIN,
            ],
        ];
    }

    /**
     * @param $configFile
     * @param $classError
     * @param $errorTarget
     *
     * @dataProvider provideDataForTestsWithFailure
     */
    public function testThrownErrorAndSendingMailWhenAnErrorOccurred(
        $configFile,
        $classError,
        $errorTarget
    ) {
        $command = $this->launchMigrateFileFromDistantCommandWithRealLogger(
            $this->getTestConfigFileFolderPath() . $configFile,
            self::YAML_CONTEXT_FILE_PATH
        );
        $this->assertThatErrorHasThrownAndEmailSent(
            $classError,
            $errorTarget,
            $command
        );
    }

    public function testSendingToAdminWhenDistantDirectoryHasFileToMigrateAndFileAreMigrated() {
        // we add a temporary file to the
        // distant folder before launching the command
        $fileName = $this->getCurrentContextualDir() . '/../../portable_ftp_server/distant_folder/test.txt';
        touch($fileName);
        $this->launchMigrateFileFromDistantCommandWithRealLogger(
            $this->getTestConfigFileFolderPath() . 'configFileWithoutError.yaml',
            self::YAML_CONTEXT_FILE_PATH
        );
        $this->assertThatEmailWasSent();
    }

    public function testSendingToAdminWhenDistantDirectoryDoNotHaveFileToMigrate() {
        $this->launchMigrateFileFromDistantCommandWithRealLogger(
            $this->getTestConfigFileFolderPath() . 'configFileWithoutError.yaml',
            self::YAML_CONTEXT_FILE_PATH
        );
        $this->assertThatEmailWasSent();
    }

    /**
     * @param $configFile
     * @param $yamlContextFilePath
     * @return MigrateFileFromDistant
     */
    private function launchMigrateFileFromDistantCommandWithRealLogger(
        $configFile,
        $yamlContextFilePath
    ) {
        $command = new MigrateFileFromDistant($this->getRealLogger());
        $this->commandTester = new CommandTester(
            $command
        );
        $this->commandTester->execute(
            [
                $yamlContextFilePath => $configFile,
            ]
        );

        return $command;
    }
}