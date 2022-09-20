<?php

namespace Blomstra\S3Assets\Frontend;

use Flarum\Frontend\Compiler\RevisionCompiler;
use Flarum\Frontend\Compiler\VersionerInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;

class Versioner implements VersionerInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    const REVISION_KEY = 's3assets.revision';

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function putRevision(string $file, ?string $revision): void
    {
        $manifest = $this->getManifest();

        if ($revision) {
            $manifest[$file] = $revision;
        } else {
            unset($manifest[$file]);
        }

        $this->settings->set(self::REVISION_KEY, json_encode($manifest));
    }

    public function getRevision(string $file): ?string
    {
        return Arr::get($this->getManifest(), $file);
    }

    private function getManifest(): array
    {
        return json_decode($this->settings->get(self::REVISION_KEY, '{}'), true);
    }
}
