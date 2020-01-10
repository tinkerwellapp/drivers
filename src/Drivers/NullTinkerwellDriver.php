<?php

class NullTinkerwellDriver extends TinkerwellDriver
{

    public function canBootstrap($projectPath): bool
    {
        return false;
    }

    public function bootstrap($projectPath)
    {
        if (file_exists($projectPath . '/vendor/autoload.php')) {
            require $projectPath . '/vendor/autoload.php';
        }
    }
}