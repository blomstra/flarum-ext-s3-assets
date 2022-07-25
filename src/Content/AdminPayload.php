<?php

namespace Blomstra\S3Assets\Content;

use Flarum\Frontend\Document;
use Illuminate\Support\Arr;

class AdminPayload
{
    public function __invoke(Document $document)
    {
        $config = resolve('config');
        $document->payload['s3SetByEnv'] = Arr::get($config->offsetGet('filesystems'), 'disks.s3.set_by_environment');
    }
}
