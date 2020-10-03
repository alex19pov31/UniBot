<?php


namespace UniBot\Events;


class DeleteEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'delete';
    }
}