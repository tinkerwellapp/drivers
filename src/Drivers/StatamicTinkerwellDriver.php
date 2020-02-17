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
    protected $aliasMap;
    protected $aliases = [
        'App\\',
        'Statamic\Facades',
        'Statamic\Support\Arr',
        'Statamic\Support\Str',
    ];

    public function bootstrap($projectPath)
    {
        parent::bootstrap($projectPath);

        $this->registerAliases($projectPath);
    }

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

    protected function registerAliases($projectPath)
    {
        $classmap = $projectPath.'/vendor/composer/autoload_classmap.php';

        $this->aliasMap = collect(require $classmap)->filter(function ($path, $class) {
            return Str::startsWith($class, $this->aliases);
        })->map(function ($path, $original) {
            return class_basename($original);
        })->unique()->flip();

        spl_autoload_register([$this, 'aliasClass']);
    }

    public function aliasClass($class)
    {
        if (Str::contains($class, '\\')) {
            return;
        }

        if ($fullName = $this->aliasMap[$class] ?? false) {
            echo "[!] Aliasing '{$class}' to '{$fullName}' for this Tinker session.\n";
            class_alias($fullName, $class);
        }
    }
}
