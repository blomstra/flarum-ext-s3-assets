<?php

namespace Blomstra\S3Assets\Extend;

use Blomstra\S3Assets\Driver\Config;
use Blomstra\S3Assets\Driver\S3Driver;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extend\Filesystem;
use Flarum\Extension\Extension;
use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;
use Illuminate\Contracts\Container\Container;

class Drivers implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        /** @var Config $config */
        $config = $container->make(Config::class);

        if (! $config->valid()) {
            return;
        }

        (new Filesystem())
            ->driver('s3', S3Driver::class)
            ->driver('local', S3Driver::class)
            ->disk('flarum-assets', fn (Paths $paths, UrlGenerator $url) => [
                'root' => '/assets',
                'url' => $config->config()['url'] . '/assets'
            ])
            ->disk('flarum-avatars', fn (Paths $paths, UrlGenerator $url) => [
                'root' => '/avatars',
                'url' => $config->config()['url'] . '/avatars'
            ])
            ->extend($container, $extension);
    }
}
