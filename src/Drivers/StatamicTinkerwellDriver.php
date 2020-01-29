<?php

use Statamic\Statamic;
use Statamic\Support\Str;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\Separator;

class StatamicTinkerwellDriver extends LaravelTinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/vendor/statamic/cms');
    }

    public function contextMenu()
    {
        return array_merge(parent::contextMenu(), [
            Separator::create(),
            
            Label::create('Detected Statamic v' . Statamic::version()),

            Submenu::create(
                'Please',
                collect(Artisan::all())
                    ->filter(function ($command, $key) {
                        return Str::startsWith($key, 'statamic:');
                    })
                    ->map(function ($command, $key) {
                        return SetCode::create(Str::after($key, 'statamic:'), "Artisan::call('" . $key . "', []);\nArtisan::output();");
                    })->values()->toArray()
            ),

            OpenURL::create('Documentation', Statamic::docsUrl('/')),
        ]);
    }
}
