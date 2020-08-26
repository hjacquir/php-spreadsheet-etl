<?php
/**
 * User: h.jacquir
 * Date: 05/06/2020
 * Time: 13:54
 */

namespace Hj\Strategy\Notifier;

use Hj\Config\MailsConfig;
use Hj\Directory\WaitingDirectory;

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
     * @var MailsConfig
     */
    private $mailsConfig;

    /**
     * NotifyAllOnceWhenNoFileForExtraction constructor.
     * @param MailsConfig $mailsConfig
     * @param WaitingDirectory $waitingDirectory
     */
    public function __construct(
        MailsConfig $mailsConfig,
        WaitingDirectory $waitingDirectory
    )
    {
        $this->mailsConfig = $mailsConfig;
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
     * @return array
     * @throws \Hj\Exception\KeyNotExist
     */
    public function getSendTo()
    {
        $users = $this->mailsConfig->getUsers()->getValue();
        $admins = $this->mailsConfig->getAdmins()->getValue();

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