<?php

namespace Blomstra\S3Assets\Provider;

use Blomstra\S3Assets\Frontend\Versioner;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Frontend\Compiler\VersionerInterface;

class S3DiskProvider extends AbstractServiceProvider
{
    public static bool $bindVersioner = true;

    public function register()
    {
        if (static::$bindVersioner) {
            $this->container->bind(VersionerInterface::class, Versioner::class);
        }
    }
}
