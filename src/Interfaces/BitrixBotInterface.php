<?php


namespace UniBot\Interfaces;


interface BitrixBotInterface
{
    public static function addMessage(array $bot, array $messageFields);
    public static function startWriting(array $bot, $dialogId, $userName = '');
    public static function deleteMessage(array $bot, $messageId);
    public static function updateMessage(array $bot, array $messageFields);
    public static function likeMessage(array $bot, $messageId, $action = 'AUTO');
    public static function register(array $fields);
    public static function unRegister(array $bot);
    public static function getBotId();
}