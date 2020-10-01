<?php


namespace UniBot;


class VKMessage extends BaseMessage
{

    public function reply(string $message, array $options = null): int
    {
        $options['reply_to'] = $this->messageId;
        return $this->answer($message, $options);
    }
}