<?php

namespace Blomstra\S3Assets\Driver;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Validation\Factory;

class Config
{
    public function __construct(protected SettingsRepositoryInterface $settings)
    {}

    public function valid(): bool
    {
        $validator = resolve(Factory::class)->make($this->config(), [
            'driver' => 'required|in:s3',
            'key' => 'required|string',
            'secret' => 'required|string',
            'region' => 'required|string',
            'bucket' => 'required|string',
            'url' => 'required|url',
            'endpoint' => 'required|url',
            'use_path_style_endpoint' => 'required|bool',
            'options.ACL' => 'required|string',
            'set_by_environment' => 'required|bool',
        ]);

        return $validator->passes();
    }

    public function config(): array
    {
        $bucket = env('AWS_BUCKET', $this->settings->get('fof-upload.awsS3Bucket'));
        $region = env('AWS_DEFAULT_REGION', $this->settings->get('fof-upload.awsS3Region'));
        $cdnUrl = env('AWS_URL', $this->settings->get('fof-upload.cdnUrl'));
        $pathStyle = (bool) (env('AWS_PATH_STYLE_ENDPOINT', $this->settings->get('fof-upload.awsS3UsePathStyleEndpoint')));

        if (! $cdnUrl) {
            $cdnUrl = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $region);
            $pathStyle = false;
        }

        $setByEnv = (env('AWS_ACCESS_KEY_ID') || env('AWS_SECRET_ACCESS_KEY') || env('AWS_ENDPOINT'));

        return [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', $this->settings->get('fof-upload.awsS3Key')),
            'secret' => env('AWS_SECRET_ACCESS_KEY', $this->settings->get('fof-upload.awsS3Secret')),
            'region' => $region,
            'bucket' => $bucket,
            'url' => $cdnUrl,
            'endpoint' => env('AWS_ENDPOINT', $this->settings->get('fof-upload.awsS3Endpoint')),
            'use_path_style_endpoint' => $pathStyle,
            'set_by_environment' => $setByEnv,
            'options' => [
                'ACL' => env('AWS_ACL', $this->settings->get('fof-upload.awsS3ACL')),
            ]
        ];
    }
}
