<?php


namespace UniBot\Events;


class DeleteChatEvent extends BaseEvent
{
    public function getCode(): string
    {
        return 'delete_chat';
    }
}