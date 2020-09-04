<?php
/**
 * User: h.jacquir
 * Date: 12/05/2020
 * Time: 15:42
 */

namespace Hj\Tests\Functional\Command;

use Hj\Command\CheckIfFileWaitingForExtractionExist;
use Hj\Helper\Tests\AbstractFunctionalTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CheckIfFileWaitingForExtractionExistTest
 * @package Hj\Tests\Functional\Command
 * @covers \Hj\Command\CheckIfFileWaitingForExtractionExist
 */
class CheckIfFileWaitingForExtractionExistTest extends AbstractFunctionalTestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    public function testThatMailForAdminAndUserAreSentWhenNoFileForExtractExist()
    {
        $this->copyContextualTestFileWaitingToDistantWaitingFolder("noFileWaitingForExtraction");
        // we run the command generating a
        // admin error having previously launched the flag reset command
        $this->launchCommandWithRealLogger(
            $this->getTestConfigFileFolderPath() . "configFileWithoutError.yaml",
            self::YAML_CONTEXT_FILE_PATH
        );
        // we make sure that the emails are sent
        $this->assertThatEmailWasSent();
        // we make sure that two emails have been sent: 1 for the admin and the other for the user
        $this->assertCountSendedMail(2);
    }


    /**
     * @param $configFile
     * @param $yamlContextFilePath
     *
     * @return CheckIfFileWaitingForExtractionExist
     */
    private function launchCommandWithRealLogger(
        $configFile,
        $yamlContextFilePath
    ) {
        $command = new CheckIfFileWaitingForExtractionExist($this->getRealLogger());
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

    /**
     * @return string
     */
    protected function getCurrentContextualDir()
    {
        return __DIR__;
    }
}