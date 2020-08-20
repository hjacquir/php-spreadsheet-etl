<?php
/**
 * User: h.jacquir
 * Date: 21/07/2020
 * Time: 17:34
 */

namespace Hj\Config;

use Hj\Yaml\Child\Admins;
use Hj\Yaml\Child\From;
use Hj\Yaml\Child\Users;

/**
 * Class MailsConfig
 * @package Hj\Config
 */
class MailsConfig implements Config
{
    /**
     * @var From
     */
    private $from;

    /**
     * @var Users
     */
    private $users;

    /**
     * @var Admins
     */
    private $admins;

    /**
     * MailsConfig constructor.
     * @param From $from
     * @param Users $users
     * @param Admins $admins
     */
    public function __construct(
        From $from,
        Users $users,
        Admins $admins
    )
    {
        $this->from = $from;
        $this->users = $users;
        $this->admins = $admins;
    }

    /**
     * @return From
     */
    public function getFrom(): From
    {
        return $this->from;
    }

    /**
     * @return Users
     */
    public function getUsers(): Users
    {
        return $this->users;
    }

    /**
     * @return Admins
     */
    public function getAdmins(): Admins
    {
        return $this->admins;
    }
}