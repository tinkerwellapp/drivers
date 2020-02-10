<?php

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Console\Cli;
use Magento\Framework\Console\CommandList;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\Submenu;
use TYPO3\PharStreamWrapper\Behavior;
use TYPO3\PharStreamWrapper\Interceptor\PharMetaDataInterceptor;
use TYPO3\PharStreamWrapper\Manager;
use TYPO3\PharStreamWrapper\PharStreamWrapper;

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
        return file_exists($projectPath . '/bin/magento');
    }

    public function bootstrap($projectPath)
    {
        Manager::initialize((new Behavior())->withAssertion(new PharMetaDataInterceptor()));

        require $projectPath . '/app/bootstrap.php';
        stream_wrapper_register('phar', PharStreamWrapper::class);

        $bootstrap = Bootstrap::create(BP, $_SERVER);

        $this->objectManager = $bootstrap->getObjectManager();

        $state = $this->objectManager->get('Magento\Framework\App\State');
        $state->setAreaCode('frontend');

        $this->commandList = $this->objectManager->get(CommandList::class)->getCommands();

        usort($this->commandList, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->version = $this->objectManager->get(ProductMetadata::class)->getVersion();
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Magento v' . $this->version),
            SetCode::create('Cache flush', '$runCliCommand(\'cache:flush\');'),
            Submenu::create('CLI', $this->cliSubmenu()),
        ];
    }

    public function getAvailableVariables(): array
    {
        return [
            'objectManager' => $this->objectManager,
            'runCliCommand' => $this->getCliCommandFunction(),
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
