<?php

namespace Blomstra\S3Assets\Driver;

use Blomstra\S3Assets\Driver\Config as DriverConfig;
use Flarum\Filesystem\DriverInterface;
use Flarum\Foundation\Config;
use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;

class S3Driver implements DriverInterface
{
    protected FilesystemManager $manager;

    public function __construct(protected Paths $paths, protected DriverConfig $config)
    {
        $this->manager = new FilesystemManager(resolve(Container::class));
    }

    public function build(
        string $diskName,
        SettingsRepositoryInterface $settings,
        Config $config,
        array $localConfig
    ): Cloud {
        $root = Arr::get($localConfig, 'root');
        $root = str_replace($this->paths->public, '', $root);

        return $this->manager->createS3Driver(array_merge(
            $this->config->config(),
            ['root' => $root]
        ));
    }
}
