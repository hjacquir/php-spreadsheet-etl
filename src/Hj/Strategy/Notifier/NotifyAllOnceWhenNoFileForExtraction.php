<?php
/**
 * User: h.jacquir
 * Date: 05/06/2020
 * Time: 13:54
 */

namespace Hj\Strategy\Notifier;

use Hj\Directory\WaitingDirectory;
use Hj\YamlConfigLoader;

/**
 * Class NotifyAllOnceWhenNoFileForExtraction
 * @package Hj\Strategy\Notifier
 */
class NotifyAllOnceWhenNoFileForExtraction implements NotifierStrategy
{
    /**
     * @var WaitingDirectory
     */
    private $waitingDirectory;

    /**
     * NotifyAllOnceWhenNoFileForExtraction constructor.
     * @param WaitingDirectory $waitingDirectory
     */
    public function __construct(WaitingDirectory $waitingDirectory)
    {
        $this->waitingDirectory = $waitingDirectory;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return false === $this->waitingDirectory->hasFiles();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return [];
    }

    /**
     * @param YamlConfigLoader $configLoader
     * @return array
     */
    public function getSendTo(YamlConfigLoader $configLoader)
    {
        $users = $configLoader->getUsersMails();
        $admins = $configLoader->getAdminsMails();

        return array_merge($users, $admins);
    }

    /**
     * @return string
     */
    public function getBodyMessage()
    {
        return "EDIR s'est bien éxécuté, mais la file d'attente des fichiers candidats à l'extraction est vide. " .
            "En effet, aucun fichier n'a été déposé récemment." .
            "\n" .
            "A bientôt.";
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return "EDIR : aucun fichier à extraire";
    }
}