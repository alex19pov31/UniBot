<?php


namespace UniBot\Events;


use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\MessageInterface;

class InitChatEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'initChat';
    }
}