<?php

namespace Blomstra\S3Assets\Provider;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class S3DiskProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->extend('config', function (Repository $config) {
            /** @var Paths $paths */
            $paths = resolve(Paths::class);

            /** @var UrlGenerator $url */
            $url = resolve(UrlGenerator::class);

            $filesystems = $this->illuminateFilesystemsConfig();
            $s3 = Arr::get($filesystems, 'disks.s3');

            foreach ($this->getFlarumDisks() as $disk => $closure) {
                /** @var array $diskConfig */
                $diskConfig = $closure($paths, $url);

                $filesystems['disks'][$disk] = array_merge($s3, [
                    'root' => Str::afterLast($diskConfig['root'], '/'),
                    'visibility' => 'public',
                ]);
            }

            $config->set('filesystems', $filesystems);

            return $config;
        });

        $this->container->extend('filesystem', function (\Flarum\Filesystem\FilesystemManager $manager) {
            return new FilesystemManager($this->container);
        });

        $this->container->singleton('filesystem.disk', function (Container $container) {
            return $container['filesystem']->disk($this->getDefaultDriver());
        });
    }

    /**
     * Get the default file driver.
     *
     * @return string
     */
    protected function getDefaultDriver(): string
    {
        return $this->container['config']['filesystems.default'];
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

    /**
     * Generate the Illuminate filesystems array from environment variables or settings.
     *
     * @return array
     */
    protected function illuminateFilesystemsConfig(): array
    {
        // TODO: Run the array via a validator.

        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve(SettingsRepositoryInterface::class);
        // Sharing settings keys with fof/upload.

        return [
            'default' => 's3',
            'disks' => [
                's3' => [
                    'driver' => 's3',
                    'key' => env('AWS_ACCESS_KEY_ID', $settings->get('fof-upload.awsS3Key')),
                    'secret' => env('AWS_SECRET_ACCESS_KEY', $settings->get('fof-upload.awsS3Secret')),
                    'region' => env('AWS_DEFAULT_REGION', $settings->get('fof-upload.awsS3Region')),
                    'bucket' => env('AWS_BUCKET', $settings->get('fof-upload.awsS3Bucket')),
                    'url' => env('AWS_URL', $settings->get('fof-upload.cdnUrl')),
                    'endpoint' => env('AWS_ENDPOINT', $settings->get('fof-upload.awsS3Endpoint')),
                    'use_path_style_endpoint' => env('AWS_PATH_STYLE_ENDPOINT', (bool) $settings->get('fof-upload.awsS3UsePathStyleEndpoint'))
                ],
            ]
        ];
    }
}
