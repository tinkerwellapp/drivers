<?php

use Tinkerwell\ContextMenu\Label;

class Drupal7TinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        $projectPath = $this->getDrupalPath($projectPath);

        return file_exists("{$projectPath}/misc/drupal.js");
    }

    public function bootstrap($projectPath)
    {
        $projectPath = $this->getDrupalPath($projectPath);
        chdir($projectPath);

        define('DRUPAL_ROOT', getcwd());

        $_SERVER['HTTP_HOST'] = 'default';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_SOFTWARE'] = $_SERVER['HTTP_USER_AGENT'] = null;
        $_SERVER['SCRIPT_FILENAME'] = DRUPAL_ROOT . '/index.php';

        require_once(DRUPAL_ROOT . '/includes/bootstrap.inc');
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Drupal v' . VERSION)
        ];
    }

    public function getDrupalPath($projectPath)
    {
        $paths = array_map(function ($subDir) use ($projectPath) {
            return "$projectPath/$subDir";
        }, $this->possibleSubdirectories());

        $foundPaths = array_filter($paths, function ($path) {
            return file_exists($path);
        });

        if (!empty($foundPaths)) {
            return array_shift($foundPaths);
        }

        return $projectPath;
    }

    private function possibleSubdirectories()
    {
        return ['docroot', 'web'];
    }
}
