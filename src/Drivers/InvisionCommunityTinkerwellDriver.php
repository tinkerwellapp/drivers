<?php

use Tinkerwell\ContextMenu\Label;

class InvisionCommunityTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/applications/core/data/application.json');
    }

    public function bootstrap($projectPath)
    {
        require_once $projectPath . '/init.php';
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Invision Community v' . \IPS\Application::load('core')->version),
        ];
    }
}
