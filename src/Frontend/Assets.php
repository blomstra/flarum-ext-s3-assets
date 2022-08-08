<?php

namespace Blomstra\S3Assets\Frontend;

use Flarum\Frontend\Compiler\JsCompiler;
use Flarum\Frontend\Compiler\LessCompiler;

class Assets extends \Flarum\Frontend\Assets
{
    protected function makeLessCompiler(string $filename): LessCompiler
    {
        $compiler = new LessCompiler($this->assetsDir, $filename, resolve(Versioner::class));

        if ($this->cacheDir) {
            $compiler->setCacheDir($this->cacheDir.'/less');
        }

        if ($this->lessImportDirs) {
            $compiler->setImportDirs($this->lessImportDirs);
        }

        if (isset($this->lessImportOverrides) && $this->lessImportOverrides) {
            $compiler->setLessImportOverrides($this->lessImportOverrides);
        }

        if (isset($this->fileSourceOverrides) && $this->fileSourceOverrides) {
            $compiler->setFileSourceOverrides($this->fileSourceOverrides);
        }

        $compiler->setCustomFunctions($this->customFunctions);

        return $compiler;
    }

    protected function makeJsCompiler(string $filename): JsCompiler
    {
        return new JsCompiler($this->assetsDir, $filename, resolve(Versioner::class));
    }
}
