<?php

namespace LivetexTest\System;


class Views
{
    private static $loader;

    public static function setLoader($loader) {
        self::$loader = $loader;
    }

    /**
     * @return \Twig_Environment
     */
    public static function getLoader() {
        return self::$loader;
    }
}
