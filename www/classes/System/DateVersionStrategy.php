<?php

namespace LivetexTest\System;


use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class DateVersionStrategy implements VersionStrategyInterface
{

    public function __construct()
    {
    }

    public function getVersion($path)
    {
        return filemtime($path);
    }

    public function applyVersion($path)
    {
        return sprintf('%s?v=%s', $path, $this->getVersion($path));
    }
}