<?php


namespace UniBot\Events;


use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\MessageInterface;

abstract class BaseEvent implements EventInterface
{
    protected $chatId;
    protected $data;
    /**
     * @var MessageInterface
     */
    protected $message;

    public function __construct($data, $chatId, MessageInterface $message)
    {
        $this->data = $data;
        $this->chatId = $chatId;
        $this->message = $message;
    }

    abstract public function getCode(): string;

    public function getData()
    {
        return $this->data;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
}