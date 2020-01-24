<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

class OctoberCMSTinkerwellDriver extends TinkerwellDriver
{

    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/index.php') &&
            file_exists($projectPath . '/artisan');
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
            Label::create('Detected OctoberCMS'),

            Submenu::create('Artisan', collect(Artisan::all())->map(function ($command, $key) {
                return SetCode::create($key, "Artisan::call('" . $key . "', []);\nArtisan::output();");
            })->values()->toArray()),

            OpenURL::create('OctoberCMS Docs', 'https://octobercms.com/docs/cms/themes'),
        ];
    }
}
