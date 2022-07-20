<?php

namespace Blomstra\S3Assets\Console;

use Flarum\Console\AbstractCommand;

class MoveAssetsCommand extends AbstractCommand
{
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
        // TODO: Iterate over known paths and move files to their S3 based disks.
    }
}
