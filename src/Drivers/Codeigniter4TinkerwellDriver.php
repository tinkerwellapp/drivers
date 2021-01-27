<?php
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\Separator;

class Codeigniter4TinkerwellDriver extends TinkerwellDriver
{
    private $version;

    public function canBootstrap($projectPath): bool
    {
        return file_exists($projectPath . '/public/index.php')
            && is_dir($projectPath . '/vendor/codeigniter4/framework');
    }

    public function bootstrap($projectPath)
    {
        define('FCPATH', $projectPath . '/public' . DIRECTORY_SEPARATOR);
        require $projectPath . '/app/Config/Paths.php';
        $paths = new Config\Paths();
        chdir(FCPATH);
        $app = require rtrim($paths->systemDirectory, '/ ') . '/bootstrap.php';

        $this->version = CodeIgniter\CodeIgniter::CI_VERSION;
    }

    public function contextMenu()
    {
        return array_merge(parent::contextMenu(), [
            Separator::create(),
            Label::create('codeigniter4 v' . $this->version),
            //OpenURL::create('official site', 'https://codeigniter.com/'),
            OpenURL::create('Documentation', 'https://codeigniter.com/user_guide/index.html'),
        ]);
    }
}