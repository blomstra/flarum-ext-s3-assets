<?php

namespace Blomstra\S3Assets\Frontend\Compilers;

class JsCompiler extends \Flarum\Frontend\Compiler\JsCompiler
{
    public function flush()
    {
        // @nope
        // We disabled this because random asset flushing through cache clearing would brick communities,
        // instead we demand cache recompilation!

        $this->commit();
    }
}
