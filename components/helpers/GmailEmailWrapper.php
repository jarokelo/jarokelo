<?php

namespace app\components\helpers;

use PhpMimeMailParser\Parser;
use PhpMimeMailParser\Attachment;

/**
 *
 */
class GmailEmailWrapper
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @param Parser $parser
     */
    public function __construct($parser)
    {
        $this->parser = $parser;
    }

    /**
     * Return the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->parser->getHeader('subject');
    }

    /**
     * Return from header
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->parser->getHeader('from');
    }

    /**
     * Return from email address
     *
     * @return string
     */
    public function getFromAddress()
    {
        $email = mailparse_rfc822_parse_addresses($this->parser->getHeader('from'));
        return $email[0]['address'];
    }

    /**
     * Return to header
     *
     * @return string
     */
    public function getTo()
    {
        return $this->parser->getHeader('to');
    }

    /**
     * Return from email address
     *
     * @return string
     */
    public function getToAddress()
    {
        $email = mailparse_rfc822_parse_addresses($this->parser->getHeader('to'));
        return $email[0]['address'];
    }

    /**
     * Attachments
     *
     * @return Attachment[]
     */
    public function getAttachments()
    {
        return $this->parser->getAttachments();
    }

    /**
     * Text content
     *
     * @return string
     */
    public function getBodyText()
    {
        return $this->parser->getMessageBody('text');
    }

    /**
     * HTML content
     *
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->parser->getMessageBody('html');
    }
}
