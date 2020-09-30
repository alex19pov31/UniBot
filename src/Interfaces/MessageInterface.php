<?php

namespace UniBot\Interfaces;

interface MessageInterface
{
    /**
     * @param string $message
     * @param array|null $options
     * @return int
     */
    public function answer(string $message, array $options = null): int;

    /**
     * @param string $message
     * @param array|null $options
     * @return int
     */
    public function reply(string $message, array $options = null): int;

    /**
     * @return string
     */
    public function getMessageText(): string;

    /**
     * @return bool
     */
    public function isCommand(): bool;
}