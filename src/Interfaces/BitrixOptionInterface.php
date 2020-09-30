<?php


namespace UniBot\Interfaces;


interface BitrixOptionInterface
{
    public static function get($moduleId, $name, $default = "", $siteId = false);
}