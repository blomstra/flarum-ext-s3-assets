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
    /**
     * @var boolean
     */
    protected $failedValidation = false;
    
    public function boot()
    {
        $this->container->extend('config', function (Repository $config) {
            /** @var Paths $paths */
            $paths = resolve(Paths::class);

            /** @var UrlGenerator $url */
            $url = resolve(UrlGenerator::class);

            $filesystems = $this->illuminateFilesystemsConfig();
            $s3 = Arr::get($filesystems, 'disks.s3');

            if (! $this->configValid($s3)) {
                // When validation fails, we don't extend the configuration, leave the disks 'as-was'
                // so that the forum is still accessible.
                $this->failedValidation = true;
            } else {
                foreach ($this->getFlarumDisks() as $disk => $closure) {
                    /** @var array $diskConfig */
                    $diskConfig = $closure($paths, $url);

                    // Maintain compatibility with previous implementations where profile covers used 'profile-covers' for the root, rather than 'covers'.
                    $root = $disk === 'sycho-profile-cover' ? 'profile-covers' : Str::afterLast($diskConfig['root'], '/');
    
                    $filesystems['disks'][$disk] = array_merge($s3, [
                        'root' => $root
                    ]);
                }

                $config->set('filesystems', $filesystems);
            }

            return $config;
        });

        $this->container->extend('filesystem', function (\Flarum\Filesystem\FilesystemManager $manager) {
            return $this->failedValidation ? $manager : new FilesystemManager($this->container);
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
        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve(SettingsRepositoryInterface::class);
        // Sharing settings keys with fof/upload.

        $bucket = getenv('AWS_BUCKET') ?? $settings->get('fof-upload.awsS3Bucket');
        $region = getenv('AWS_DEFAULT_REGION') ?? $settings->get('fof-upload.awsS3Region');
        $cdnUrl = getenv('AWS_URL') ?? $settings->get('fof-upload.cdnUrl');
        $pathStyle = (bool) (getenv('AWS_PATH_STYLE_ENDPOINT') ?? $settings->get('fof-upload.awsS3UsePathStyleEndpoint'));
        
        if (! $cdnUrl) {
            $cdnUrl = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $region);
            $pathStyle = false;
        }

        $setByEnv = (bool) (getenv('AWS_ACCESS_KEY_ID') || getenv('AWS_SECRET_ACCESS_KEY') || getenv('AWS_ENDPOINT'));

        return [
            'default' => 's3',
            'disks' => [
                's3' => [
                    'driver' => 's3',
                    'key' => getenv('AWS_ACCESS_KEY_ID') ?? $settings->get('fof-upload.awsS3Key'),
                    'secret' => getenv('AWS_SECRET_ACCESS_KEY') ?? $settings->get('fof-upload.awsS3Secret'),
                    'region' => $region,
                    'bucket' => $bucket,
                    'url' => $cdnUrl,
                    'endpoint' => getenv('AWS_ENDPOINT') ?? $settings->get('fof-upload.awsS3Endpoint'),
                    'use_path_style_endpoint' => $pathStyle,
                    'visibility' => getenv('AWS_ACL') ?? $settings->get('fof-upload.awsS3ACL'),
                    'set_by_environment' => $setByEnv,
                ],
            ]
        ];
    }

    protected function configValid(array $s3Config): bool
    {
        //dd($s3Config);
        return true;
    }
}
