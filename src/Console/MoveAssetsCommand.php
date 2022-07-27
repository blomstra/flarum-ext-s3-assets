<?php

namespace Blomstra\S3Assets\Console;

use Flarum\Console\AbstractCommand;
use Flarum\Foundation\Paths;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory;

class MoveAssetsCommand extends AbstractCommand
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Cloud
     */
    protected $assetsDisk;

    /**
     * @var Cloud
     */
    protected $avatarDisk;

    public function __construct(Container $container, Factory $factory, Paths $paths)
    {
        $this->container = $container;
        $this->assetsDisk = $factory->disk('flarum-assets');
        $this->avatarDisk = $factory->disk('flarum-avatars');
        $this->paths = $paths;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('s3:move')
            ->setDescription('Move assets from local filesystem to S3 disks');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        /** @var \Illuminate\Filesystem\Filesystem $localFilesystem */
        $localFilesystem = $this->container->make('files');

        // Move avatars
        foreach ($localFilesystem->allFiles($this->paths->public . '/assets/avatars') as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $written = $this->avatarDisk->put($file->getRelativePathname(), $file->getContents());

            if ($written) {
                $localFilesystem->delete($file);
            } else {
                throw new \Exception('File did not move');
            }
        }

        // Move other assets
        foreach ($localFilesystem->allFiles($this->paths->public . '/assets') as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $written = $this->assetsDisk->put($file->getRelativePathname(), $file->getContents());

            if ($written) {
                $localFilesystem->delete($file);
            } else {
                throw new \Exception('File did not move');
            }
        }
    }
}
