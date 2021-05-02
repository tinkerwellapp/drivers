<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class PerchTinkerwellDriver extends TinkerwellDriver
{
    private $dir = 'perch';

    public function canBootstrap($projectPath)
    {
        if (file_exists($projectPath . '/perch/runtime.php')) {
            return true;
        }

        $perch_class = glob('*/core/lib/Perch.class.php');
        if (count($perch_class)) {
            $this->dir = strtok($perch_class[0], '/');
            return true;
        }

        return false;
    }



    public function bootstrap($projectPath)
    {
        require $projectPath . '/' . $this->dir . '/runtime.php';
    }



    public function contextMenu()
    {
        $Perch = Perch::fetch();

        return [
            Label::create('Detected Perch ' . (PERCH_RUNWAY ? 'Runway v' : 'v') . $Perch->version),
            OpenURL::create('Documentation', 'https://docs.grabaperch.com/'),
        ];
    }
}
