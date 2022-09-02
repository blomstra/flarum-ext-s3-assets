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
        if ($storedManifest = $this->settings->get(self::REVISION_KEY)) {
            $manifest = json_decode($storedManifest, true);
        } else {
            $manifest = [];
        }

        $manifest[$file] = $revision ?? RevisionCompiler::EMPTY_REVISION;

        $this->settings->set(self::REVISION_KEY, json_encode($manifest));
    }

    public function getRevision(string $file): ?string
    {
        $storedManifest = $this->settings->get(self::REVISION_KEY);

        if ($storedManifest) {
            $manifest = json_decode($storedManifest, true);

            return Arr::get($manifest, $file);
        }

        return null;
    }
}
