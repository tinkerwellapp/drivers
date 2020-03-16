<?php

use Helhum\Typo3Console\CompatibilityClassLoader;
use Helhum\Typo3Console\Core\Kernel;
use Helhum\Typo3Console\Mvc\Cli\Symfony\Input\ArgvInput;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

/**
 * Class Typo3TinkerwellDriver
 */
class Typo3TinkerwellDriver extends TinkerwellDriver
{

    /** @var string */
    protected $version = '';

    /** @var string */
    protected $documentation = '';

    /**
     * Determine if the driver can be used with the selected project path.
     * You most likely want to check the existence of project / framework specific files.
     *
     * @param string $projectPath
     * @return bool
     */
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/vendor/bin/typo3cms');
    }

    /**
     * Bootstrap TYPO3 in CLI mode using EXT:typo3_console
     *
     * @param string $projectPath
     */
    public function bootstrap($projectPath)
    {
        $classLoader = require $projectPath . '/vendor/autoload.php';
        $kernel = new Kernel(new CompatibilityClassLoader($classLoader));
        $kernel->handle(new ArgvInput());
        $this->version = TYPO3_version;
        $this->documentation = TYPO3_URL_DOCUMENTATION;
    }

    /**
     * Basic context menu, confirming TYPO3 version and a link to the TYPO3 Documentation
     *
     * @return array
     */
    public function contextMenu()
    {
        return [
            Label::create('Detected TYPO3 ' . $this->version),
            OpenURL::create('Documentation', $this->documentation),
        ];
    }
}
