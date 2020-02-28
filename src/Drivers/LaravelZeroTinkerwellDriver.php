<?php

use LaravelZero\Framework\Application;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

class LaravelZeroTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/bootstrap/app.php')
            && is_dir($projectPath . '/vendor/laravel-zero/framework');
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
            Label::create('Detected Laravel Zero v'.Application::VERSION),

            Submenu::create('Commands', collect(Artisan::all())->map(static function ($command, $key) {
                return SetCode::create($key, "Artisan::call('{$key}', []);\nArtisan::output();");
            })->values()->toArray()),

            OpenURL::create('Documentation', 'https://laravel-zero.com/docs/introduction'),
        ];
    }
}
