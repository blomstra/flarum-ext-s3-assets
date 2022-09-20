<?php

/*
 * This file is part of blomstra/s3-assets.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\S3Assets;

use Blomstra\S3Assets\Frontend\Versioner;
use Flarum\Extend;
use Flarum\Extension\Extension;
use Flarum\Frontend\Compiler\VersionerInterface;
use Illuminate\Contracts\Container\Container;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less')
        ->content(Content\AdminPayload::class),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\ServiceProvider())
        ->register(Provider\S3DiskProvider::class),

    (new Extend\Console())
        ->command(Console\MoveAssetsCommand::class),
];
