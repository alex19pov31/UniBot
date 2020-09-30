<?php


namespace UniBot;

class TelegramMessage extends BaseMessage
{
    public function reply(string $message, array $options = null): int
    {
        $options = $options ?? [];
        $options['reply_to_message_id'] = $this->messageId;
        $this->provider->sendMessage($this->chatId, $message, $options);
    }
}