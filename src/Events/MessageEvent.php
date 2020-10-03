<?php

namespace UniBot\Events;

use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\MessageInterface;

class MessageEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'message_event';
    }
}