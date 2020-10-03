<?php


namespace UniBot\Interfaces;


interface EventInterface
{
    public function getData();
    public function getChatId();
    public function getMessage(): MessageInterface;
    public function getCode(): string;
}