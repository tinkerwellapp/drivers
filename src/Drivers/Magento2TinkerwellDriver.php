<?php

use Magento\Framework\App;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Console\Cli;
use Magento\Framework\Console\CommandList;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\ObjectManagerInterface;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Submenu;

class Magento2TinkerwellDriver extends TinkerwellDriver
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $commandList;

    /**
     * @var string
     */
    private $version;

    public function canBootstrap($projectPath): bool
    {
        return file_exists($projectPath . '/app/etc/env.php');
    }

    public function bootstrap($projectPath)
    {
        require $projectPath . '/app/bootstrap.php';
        // Magento 2.3.1 removes phar stream wrapper.
        if (!in_array('phar', \stream_get_wrappers())) {
            \stream_wrapper_restore('phar');
        }

        $bootstrap = Bootstrap::create(BP, $_SERVER);

        $this->objectManager = $bootstrap->getObjectManager();

        $this->commandList = $this->objectManager->get(CommandList::class)->getCommands();

        usort($this->commandList, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->version = $this->objectManager->get(ProductMetadata::class)->getVersion();
    }

    public function getAvailableVariables()
    {
        return [
            'om' => App\ObjectManager::getInstance(),
            'tw' => new class()
            {
                public function loadArea(string $area): void
                {
                    $om           = App\ObjectManager::getInstance();
                    $appState     = $om->get(App\State::class);
                    $configLoader = $om->get(ConfigLoaderInterface::class);
                    $areaList     = $om->get(App\AreaList::class);

                    $appState->setAreaCode($area);
                    $om->configure($configLoader->load($area));
                    $areaList->getArea($area)
                        ->load(App\Area::PART_CONFIG)
                        ->load(App\Area::PART_TRANSLATE);
                }
            },
        ];
    }

    private function cliSubmenu()
    {
        $commandTemplate = <<<EOI
\$runCliCommand('%s', [
    // (optional) define the value of command arguments
    // 'fooArgument' => 'barValue',
]);
EOI;

        $commands = array_map(function (Command $command) use ($commandTemplate) {
            return SetCode::create($command->getName(), sprintf($commandTemplate, $command->getName()));
        }, $this->commandList);

        return array_values($commands);
    }

    /**
     * @return Closure
     */
    protected function getCliCommandFunction()
    {
        return function ($command, $options = []) {
            $options['command'] = $command;

            $input = new ArrayInput($options);
            $output = new BufferedOutput();

            $application = new Cli('Magento CLI');
            $application->setAutoExit(false);
            $application->run($input, $output);

            return $output->fetch();
        };
    }
}