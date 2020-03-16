<?php

use Tinkerwell\ContextMenu\Label;

class Drupal8TinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        $projectPath = $this->getDrupalPath($projectPath);

        return file_exists("{$projectPath}/core/lib/Drupal.php");
    }

    public function bootstrap($projectPath)
    {
        $projectPath = $this->getDrupalPath($projectPath);
        chdir($projectPath);
        $autoloader = require_once('autoload.php');

        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $kernel = \Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
        $kernel->boot();
        $kernel->preHandle($request);
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Drupal v' . \Drupal::VERSION)
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
