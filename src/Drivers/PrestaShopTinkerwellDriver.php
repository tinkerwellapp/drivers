<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\SetCode;

class PrestaShopTinkerwellDriver extends TinkerwellDriver
{

    public function canBootstrap($projectPath)
    {
        return is_file($projectPath . '/config/config.inc.php')
            && is_dir($projectPath . '/src/PrestaShopBundle');
    }

    public function bootstrap($projectPath)
    {
        require $projectPath . '/config/config.inc.php';
    }

    public function getAvailableVariables()
    {
        return [
            '__context' => Context::getContext(),
        ];
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected PrestaShop v' . _PS_VERSION_),
            SetCode::create(
                'Clear cache',
                "(new \PrestaShop\PrestaShop\Adapter\Cache\CacheClearer())->clearAllCaches();"
            ),
            OpenURL::create('Open shop', Context::getContext()->shop->getBaseURL(true)),
            OpenURL::create('Documentation', 'https://doc.prestashop.com/'),
        ];
    }
}
