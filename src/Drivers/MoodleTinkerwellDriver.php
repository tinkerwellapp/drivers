<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class MoodleTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/config.php')
            && file_exists($projectPath . '/course')
            && file_exists($projectPath . '/grade');
    }

    public function bootstrap($projectPath)
    {
        define('CLI_SCRIPT', true);
        require $projectPath . '/config.php';
    }

    public function contextMenu()
    {
        global $CFG;

        return [
            Label::create('Detected Moodle ' . $CFG->release),
            OpenURL::create('Developer Documentation', 'https://docs.moodle.org/dev/'),
        ];
    }
}
