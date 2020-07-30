<?php
/**
 * User: h.jacquir
 * Date: 24/01/2020
 * Time: 08:47
 */

namespace Hj\Strategy\MailHandler;

use Hj\Directory\Directory;
use Hj\Strategy\Strategy;
use Swift_Attachment;
use Swift_Message;

/**
 * Attach file to a swift message if needed (filePath is not null)
 *
 * Class AddAttachmentStrategy
 * @package Hj\Strategy\MailHandler
 */
class AddAttachmentStrategy implements Strategy
{

    /**
     * @var Swift_Message
     */
    private $swiftMessage;

    /**
     * @var null Swift_Attachment|null
     */
    private $attachMent = null;

    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var string
     */
    private $dirName;

    /**
     * AddAttachment constructor.
     * @param Swift_Message $swiftMessage
     * @param Directory $directory
     * @param string $dirName
     */
    public function __construct(
        Swift_Message $swiftMessage,
        Directory $directory,
        $dirName = ""
    )
    {
        $this->swiftMessage = $swiftMessage;
        $this->directory = $directory;
        $this->dirName = $dirName;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->directory->hasFiles($this->dirName);
    }

    public function apply()
    {
        if ($this->isAppropriate()) {
            $this->attachMent = Swift_Attachment::fromPath($this->directory->getCurrentPoppedFileName($this->dirName));
            $this->swiftMessage->attach($this->attachMent);
        }
    }
}