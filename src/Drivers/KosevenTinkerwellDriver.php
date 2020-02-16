<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class KosevenTinkerwellDriver extends TinkerwellDriver
{
    /**
     * @param string $projectPath
     * @return bool
     */
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/application/bootstrap.php');
    }

    /**
     * @param string $projectPath
     */
    public function bootstrap($projectPath)
    {
        define('EXT', '.php');
        define('DOCROOT', $projectPath . DIRECTORY_SEPARATOR);
        define('PUBLICROOT', $projectPath . '/public/');
        define('APPPATH', $projectPath . '/application/');
        define('MODPATH', $projectPath . '/modules/');
        if (file_exists($projectPath . '/system/')) {
            define('SYSPATH', $projectPath . '/system/');
        } else {
            define('SYSPATH', $projectPath . '/vendor/koseven/koseven/system/');
        }
        define('IS_CLI_REQUEST', PHP_SAPI === 'cli');

        define('KOHANA_START_TIME', microtime(true));
        define('KOHANA_START_MEMORY', memory_get_usage());

        require_once $projectPath . '/application/bootstrap.php';
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Koseven v' . \Kohana::VERSION),
            OpenURL::create('Documentation', 'https://koseven.dev/documentation'),
        ];
    }
}
