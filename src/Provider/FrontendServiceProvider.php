<?php

namespace Blomstra\S3Assets\Provider;

use Blomstra\S3Assets\Frontend\Assets;
use Flarum\Foundation\Paths;
use Illuminate\Contracts\Container\Container;

class FrontendServiceProvider extends \Flarum\Frontend\FrontendServiceProvider
{
    public function register()
    {
        $this->container->extend('flarum.assets.factory', function ($_, Container $container) {
            return function (string $name) use ($container) {
                /** @var Paths $paths */
                $paths = $container->make(Paths::class);

                $assets = new Assets(
                    $name,
                    $container->make('filesystem')->disk('flarum-assets'),
                    $paths->storage,
                    null,
                    $container->make('flarum.frontend.custom_less_functions')
                );

                $assets->setLessImportDirs([
                    $paths->vendor.'/components/font-awesome/less' => ''
                ]);

                $assets->css([$this, 'addBaseCss']);
                $assets->localeCss([$this, 'addBaseCss']);

                return $assets;
            };
        });
    }
}
