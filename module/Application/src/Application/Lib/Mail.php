<?php

/**
 * Date: 07.04.14
 * Time: 14:18
 * @author: Norman Albusberger
 *
 * The MIT License (MIT)
  Copyright (c) 2015 WasabiLib.org
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
 */

namespace Application\Lib;

use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Header\ContentType;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;
use Zend\Mime;
use Zend\Mail\Exception\InvalidArgumentException;

class Mail {

    /**
     * @var Message
     */
    protected $message;
    protected $messageType = "text";
    protected $renderer = null;
    protected $transporter = null;
    protected $attachments = [];

    const DEFAULT_CHARSET = 'utf-8';

    /**
     * @param string $from
     * @param string $name
     * @param string | \Zend\View\Model\ViewModel $body
     * @param \Zend\View\Renderer\RendererInterface $renderer
     */
    public function __construct($from = "", $name = "", RendererInterface $renderer = null) {
        if (empty($from)) {
            $from = \Application\Module::getConfig('email.from_email');
        }
        if (empty($name)) {
            $name = \Application\Module::getConfig('email.from_name');
        }
        $this->message = new Message();
        $this->message->setEncoding(self::DEFAULT_CHARSET);
        if ($from)
            $this->message->setFrom($from, $name);
        $this->renderer = $renderer;
    }

    /**
     * @param string | \Zend\View\Model\ViewModel | Mime\Part $body
     * @return $this
     */
    public function setBody($body, $charset = null) {
        $mimeMessage = new Mime\Message();
        $finalBody = null;
        if (is_string($body)) {
            // Create a Mime\Part and wrap it into a Mime\Message
            $mimePart = new Mime\Part($body);
            $mimePart->type = $body != strip_tags($body) ? Mime\Mime::TYPE_HTML : Mime\Mime::TYPE_TEXT;
            $mimePart->charset = $charset ? : self::DEFAULT_CHARSET;
            $mimeMessage->setParts([$mimePart]);
            $finalBody = $mimeMessage;
        } elseif ($body instanceof Mime\Part) {
            // Overwrite the charset if the Part object if provided
            if (isset($charset)) {
                $body->charset = $charset;
            }
            // The body is a Mime\Part. Wrap it into a Mime\Message
            $mimeMessage->setParts([$body]);
            $finalBody = $mimeMessage;
        } elseif ($body instanceof ViewModel) {
            $view = new View();
            $view->setResponse(new Response());
            $view->getEventManager()->attach(new PhpRendererStrategy($this->renderer));
            $view->render($body);
            $content = $view->getResponse()->getContent();
            $mimePart = new Mime\Part($content);
            $mimePart->type = Mime\Mime::TYPE_HTML;
            $mimePart->charset = $charset ? : self::DEFAULT_CHARSET;
            $mimeMessage->setParts([$mimePart]);
            $finalBody = $mimeMessage;
        }
        // If the body is not a string or a Mime\Message at this point, it is not a valid argument
        else {
            throw new InvalidArgumentException(sprintf(
                    'Provided body is not valid. It should be one of "%s". %s provided', implode('", "', ['string', 'Zend\Mime\Part', 'Zend\Mime\Message', 'Zend\View\Model\ViewModel']), is_object($body) ? get_class($body) : gettype($body)
            ));
        }
        // The headers Content-type and Content-transfer-encoding are duplicated every time the body is set.
        // Removing them before setting the body prevents this error
        $this->message->getHeaders()->removeHeader('contenttype');
        $this->message->getHeaders()->removeHeader('contenttransferencoding');
        $this->message->setBody($finalBody);
        return $this;
    }

    /**
     * @param string $from
     * @param string $name
     */
    public function setFrom($from, $name = null) {
        $this->message->setFrom($from, $name);
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->message->setSubject($subject);
    }

    /**
     * @param string $emailAddress
     */
    public function setTo($emailAddress) {
        $this->message->setTo($emailAddress);
    }

    /**
     * @param string $emailAddress
     */
    public function addRecipient($emailAddress) {
        $this->message->addTo($emailAddress);
    }

    /**
     * @param string $emailAddress
     */
    public function addBccRecipient($emailAddress) {
        $this->message->addBcc($emailAddress);
    }

    /**
     * @param string $emailAddress
     */
    public function addCcRecipient($emailAddress) {
        $this->message->addCc($emailAddress);
    }

    /**
     * @param string $abstractConst
     */
    protected function setMessageType($abstractConst) {
        $this->messageType = $abstractConst;
    }

    /**
     * @param mixed $transporter
     */
    public function setTransporter($transporter) {
        $this->transporter = $transporter;
    }

    /**
     * @return mixed
     */
    public function getTransporter() {
        return $this->transporter;
    }

    /**
     * @param null|\Zend\View\Renderer\RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * @return null|\Zend\View\Renderer\RendererInterface
     */
    public function getRenderer() {
        return $this->renderer;
    }

    /**
     * Sends the email.
     */
    public function send() {
        if ($this->message->getBody() == null) {
            throw new InvalidArgumentException(sprintf(
                    'Provided body is not valid. It should be one of "%s". %s provided', implode('", "', ['string', 'Zend\Mime\Part', 'Zend\Mime\Message', 'Zend\View\Model\ViewModel']), is_object($this->message->getBody()) ? get_class($this->message->getBody()) : gettype($this->message->getBody())
            ));
        } else {
            $this->attachFiles();
            $this->transporter->send($this->message);
        }
    }

    public function addAttachment($path, $filename = null) {
        if (isset($filename)) {
            $this->attachments[$filename] = $path;
        } else {
            $this->attachments[] = $path;
        }
        return $this;
    }

    /**
     * Attaches files to the message if any
     */
    private function attachFiles() {
        if (count($this->attachments) === 0) {
            return;
        }
        // Get old message parts
        $mimeMessage = $this->message->getBody();
        if (is_string($mimeMessage)) {
            $originalBodyPart = new Mime\Part($mimeMessage);
            $originalBodyPart->type = $mimeMessage != strip_tags($mimeMessage) ? Mime\Mime::TYPE_HTML : Mime\Mime::TYPE_TEXT;
            // A Mime\Part body will be wraped into a Mime\Message, ensuring we handle a Mime\Message after this point
            $this->setBody($originalBodyPart);
            $mimeMessage = $this->message->getBody();
        }
        $oldParts = $mimeMessage->getParts();
        // Generate a new Mime\Part for each attachment
        $attachmentParts = [];
        $info = new \finfo(FILEINFO_MIME_TYPE);
        foreach ($this->attachments as $key => $attachment) {
            if (!is_file($attachment)) {
                continue; // If checked file is not valid, continue to the next
            }
            // If the key is a string, use it as the attachment name
            $basename = is_string($key) ? $key : basename($attachment);
            $part = new Mime\Part(fopen($attachment, 'r'));
            $part->id = $basename;
            $part->filename = $basename;
            $part->type = $info->file($attachment);
            $part->encoding = Mime\Mime::ENCODING_BASE64;
            $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $attachmentParts[] = $part;
        }
        $body = new Mime\Message();
        $body->setParts(array_merge($oldParts, $attachmentParts));
        $this->message->setBody($body);
    }

}
