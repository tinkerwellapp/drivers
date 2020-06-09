<?php

use Tinkerwell\ContextMenu\Label;

class WordpressTinkerwellDriver extends TinkerwellDriver
{

    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/wp-load.php');
    }

    public function bootstrap($projectPath)
    {
        require $projectPath . '/wp-load.php';
    }

    public function getAvailableVariables() : array
    {
        return [
            '__blog' => get_bloginfo()
        ];
    }

    public function contextMenu() : array
    {
        return [
            Label::create('Detected Wordpress v' . get_bloginfo('version')),
        ];
    }
}
