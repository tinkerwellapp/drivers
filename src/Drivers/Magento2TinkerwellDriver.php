<?php

use Magento\Framework\App\Bootstrap;

class Magento2TinkerwellDriver extends TinkerwellDriver
{
    private $objectManager;

    public function canBootstrap($projectPath): bool
    {
        return file_exists($projectPath . '/bin/magento');
    }

    public function bootstrap($projectPath)
    {
        require $projectPath . '/app/bootstrap.php';

        $bootstrap = Bootstrap::create(BP, $_SERVER);

        $this->objectManager = $bootstrap->getObjectManager();

        $state = $this->objectManager->get('Magento\Framework\App\State');
        $state->setAreaCode('frontend');
    }

    public function getAvailableVariables(): array
    {
        return [
            'objectManager' => $this->objectManager
        ];
    }
}
