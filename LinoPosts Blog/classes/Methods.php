<?php
namespace classes;
Class Methods{
    public static function strposX($haystack, $needle, $number = 0){
            return strpos($haystack, $needle,
                $number > 1 ?
                self::strposX($haystack, $needle, $number - 1) + strlen($needle) : 0
            );
        }
}