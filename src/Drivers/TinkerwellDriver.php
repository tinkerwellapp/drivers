<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

abstract class TinkerwellDriver
{
    /**
     * Determine if the driver can be used with the selected project path.
     * You most likely want to check the existence of project / framework specific files.
     *
     * @param string $projectPath
     * @return bool
     */
    abstract public function canBootstrap($projectPath);

    /**
     * Bootstrap the application so that any executed can access the application in your desired state.
     *
     * @param string $projectPath
     */
    abstract public function bootstrap($projectPath);

    public function contextMenu()
    {
        return [
            Label::create('Tinkerwell'),

            OpenURL::create('Documentation', 'https://tinkerwell.app'),
        ];
    }

    public function getAvailableVariables()
    {
        return [];
    }

    public static function detectDriverForPath($projectPath)
    {
        $drivers = [];

        $drivers = array_merge($drivers, static::driversIn($projectPath . DIRECTORY_SEPARATOR . '.tinkerwell'));

        $drivers = array_merge($drivers, [
            'InfectionTinkerwellDriver',
            'StatamicTinkerwellDriver',
            'KirbyTinkerwellDriver',
            'MoodleTinkerwellDriver',
            'LaravelTinkerwellDriver',
            'OctoberCMSTinkerwellDriver',
            'WordpressTinkerwellDriver',
        ]);

        foreach ($drivers as $driver) {
            /** @var TinkerwellDriver $driver */
            try {
                $driver = new $driver;

                if ($driver->canBootstrap($projectPath)) {
                    return $driver;
                }
            } catch (\Throwable $e) {
                //
            }
        }

        return new NullTinkerwellDriver();
    }

    /**
     * Get all of the driver classes in a given path.
     *
     * @param string $path
     * @return array
     */
    public static function driversIn($path)
    {
        if (!is_dir($path)) {
            return [];
        }
        $drivers = [];
        $dir = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($dir);
        $regex = new RegexIterator($iterator, '/^.+TinkerwellDriver\.php$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $file) {
            require_once $file[0];
            $drivers[] = basename($file[0], '.php');
        }
        return $drivers;
    }
}
