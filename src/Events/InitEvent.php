<?php


namespace UniBot\Events;


class InitEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'init';
    }
}