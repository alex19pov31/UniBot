<?php

namespace UniBot\Events;


class MessageEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'message_event';
    }
}