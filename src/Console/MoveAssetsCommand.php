<?php

namespace Blomstra\S3Assets\Console;

use Flarum\Console\AbstractCommand;
use Flarum\Foundation\Console\AssetsPublishCommand;
use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class MoveAssetsCommand extends AbstractCommand
{
    /**
     * @var Container
     */
    protected $container;

    /** 
     * @var Factory
     */
    protected $factory;

    /**
     * @var Cloud
     */
    protected $avatarDisk;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
    * @var Paths
    */
    protected $paths;

    /**
     * @var AssetsPublishCommand
     */
    protected $publishCommand;


    public function __construct(Container $container, Factory $factory, Paths $paths, AssetsPublishCommand $publishCommand)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->avatarDisk = $factory->disk('flarum-avatars');
        $this->paths = $paths;
        $this->publishCommand = $publishCommand;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('s3:move')
            ->setDescription('Move avatars, etc from local filesystem to S3 disks, then republish remaning assets');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        /** @var Filesystem $localFilesystem */
        $localFilesystem = $this->container->make('files');

        // Move avatars
        $this->info('Moving avatars...');
        $this->moveFilesToDisk($localFilesystem, $this->paths->public . '/assets/avatars', $this->avatarDisk);

        // Move profile covers
        if (Arr::has($this->getFlarumDisks(), 'sycho-profile-cover')) {
            $this->info('Moving profile covers...');
            $coversDisk = $this->factory->disk('sycho-profile-cover');
            $this->moveFilesToDisk($localFilesystem, $this->paths->public . '/assets/covers', $coversDisk);
        }

        $this->publishCommand->run(
            new ArrayInput([]),
            new ConsoleOutput()
        );
    }

    /**
     * Get the registered disks.
     *
     * @return array
     */
    protected function getFlarumDisks(): array
    {
        return resolve('flarum.filesystem.disks');
    }

    protected function moveFilesToDisk(Filesystem $localFilesystem, string $localPath, Cloud $disk): void
    {
        foreach ($localFilesystem->allFiles($localPath) as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $this->info('Moving ' . $file->getPathname());
            $written = $disk->put($file->getRelativePathname(), $file->getContents());

            if ($written) {
                $localFilesystem->delete($file);
            } else {
                throw new \Exception('File did not move');
            }
        }
    }
}
