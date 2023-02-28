<?php

namespace Pachel\EasyFrameWork;

class Functions
{
    /**
     * @param string $dir
     * @return mixed|string
     */
    public static function checkSlash($dir) {
        if (mb_substr($dir, strlen($dir) - 1, 1) == "/") {
            return $dir;
        }
        return $dir . "/";
    }
}