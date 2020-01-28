<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Submenu;

class StatamicTinkerwellDriver extends LaravelTinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/vendor/statamic/cms');
    }

    public function contextMenu()
    {
        return array_merge(parent::contextMenu(), [
            Label::create('Detected Statamic v' . \Statamic\Statamic::version()),

            Submenu::create(
                'Please',
                collect(Artisan::all())
                    ->filter(function ($command, $key) {
                        $length = strlen('statamic');

                        return (substr($key, 0, $length) === 'statamic');
                    })
                    ->map(function ($command, $key) {
                        return SetCode::create($key, "Artisan::call('" . $key . "', []);\nArtisan::output();");
                    })->values()->toArray()
            ),

            OpenURL::create('Statamic Docs', 'https://statamic.dev'),
        ]);
    }
}
