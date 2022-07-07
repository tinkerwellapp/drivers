<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class KirbyTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/kirby/bootstrap.php');
    }

    public function bootstrap($projectPath)
    {
	define('KIRBY_HELPER_DUMP', false);

        require $projectPath . '/kirby/bootstrap.php';
        (new Kirby)->render();
    }

    public function getAvailableVariables()
    {
        return [
            'site' => site(),
            'kirby' => kirby(),
        ];
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Kirby v.' . Kirby::version()),

            OpenURL::create('Kirby Guide', 'https://getkirby.com/docs/guide'),

            OpenURL::create('Kirby Reference', 'https://getkirby.com/docs/reference'),
        ];
    }
}
