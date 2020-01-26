<?php

use Infection\Container;
use Tinkerwell\ContextMenu\Label;

final class InfectionTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/bin/infection') &&
            file_exists($projectPath . '/bin/infection-debug');
    }

    public function bootstrap($projectPath)
    {
        require_once $projectPath . '/vendor/autoload.php';
    }

    public function getAvailableVariables()
    {
        $container = Container::create();
        $container = $container->withDynamicParameters(
            null,
            '',
           false,
            'default',
            false,
            false,
            'dot',
            false,
            null,
            null,
            false,
            null,
            null,
            null,
            null,
            ''
        );

        return [
            'container' => $container,
        ];
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Infection'),
        ];
    }
}
