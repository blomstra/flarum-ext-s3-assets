<?php

namespace Blomstra\S3Assets\Driver;

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

    public function __construct(protected Paths $paths)
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
            $this->config($settings),
            ['root' => $root]
        ));
    }

    protected function config(SettingsRepositoryInterface $settings): array
    {
        $bucket = env('AWS_BUCKET', $settings->get('fof-upload.awsS3Bucket'));
        $region = env('AWS_DEFAULT_REGION', $settings->get('fof-upload.awsS3Region'));
        $cdnUrl = env('AWS_URL', $settings->get('fof-upload.cdnUrl'));
        $pathStyle = (bool) (env('AWS_PATH_STYLE_ENDPOINT', $settings->get('fof-upload.awsS3UsePathStyleEndpoint')));

        if (! $cdnUrl) {
            $cdnUrl = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $region);
            $pathStyle = false;
        }

        $setByEnv = (env('AWS_ACCESS_KEY_ID') || env('AWS_SECRET_ACCESS_KEY') || env('AWS_ENDPOINT'));

        return [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', $settings->get('fof-upload.awsS3Key')),
            'secret' => env('AWS_SECRET_ACCESS_KEY', $settings->get('fof-upload.awsS3Secret')),
            'region' => $region,
            'bucket' => $bucket,
            'url' => $cdnUrl,
            'endpoint' => env('AWS_ENDPOINT', $settings->get('fof-upload.awsS3Endpoint')),
            'use_path_style_endpoint' => $pathStyle,
            'set_by_environment' => $setByEnv,
            'options' => [
                'ACL' => env('AWS_ACL', $settings->get('fof-upload.awsS3ACL')),
            ]
        ];
    }
}
