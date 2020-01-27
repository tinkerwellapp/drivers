<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Submenu;

class StatamicTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/vendor/statamic/cms/src/Statamic.php') &&
        file_exists($projectPath . '/please');
    }

    public function bootstrap($projectPath)
    {
        require_once $projectPath . '/vendor/autoload.php';

        $app = require_once $projectPath . '/bootstrap/app.php';

        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

        $kernel->bootstrap();
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Statamic v' . \Statamic\Statamic::version()),

            Submenu::create('Artisan', collect(Artisan::all())->map(function ($command, $key) {
                return SetCode::create($key, "Artisan::call('" . $key . "', []);\nArtisan::output();");
            })->values()->toArray()),

            OpenURL::create('Statamic Docs', 'https://statamic.dev'),
        ];
    }
}
