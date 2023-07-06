<?php

use Composer\InstalledVersions;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use Shopware\Core\Kernel as CoreKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

class Shopware6TinkerwellDriver extends TinkerwellDriver
{
    protected $container;
    protected $kernel;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $appEnv;

    public function canBootstrap($projectPath): bool
    {
        return file_exists($projectPath . '/symfony.lock') &&
            (
                file_exists($projectPath . '/src/Core/Framework/ShopwareHttpException.php') || # platform repo direct
                file_exists($projectPath . '/vendor/shopware/platform/src/Core/Framework/ShopwareHttpException.php') || # platform repo as composer dep
                file_exists($projectPath . '/vendor/shopware/core/Framework/ShopwareHttpException.php') # platform repo as split in composer
            );
    }

    public function bootstrap($projectPath): void
    {
        $classLoader = require $projectPath . '/vendor/autoload.php';

        $projectRoot = $projectPath;
        if (class_exists(Dotenv::class) && (file_exists($projectRoot . '/.env.local.php') || file_exists($projectRoot . '/.env') || file_exists($projectRoot . '/.env.dist'))) {
            (new Dotenv())->usePutenv()->setProdEnvs(['prod', 'e2e'])->bootEnv($projectRoot . '/.env');
        }

        $this->version = InstalledVersions::getVersion('shopware/core');

        if ($this->version === null) {
            $this->version = InstalledVersions::getVersion('shopware/platform');
        }

        $this->appEnv = $_SERVER['APP_ENV'] ?? 'prod';

        $pluginLoader = new DbalKernelPluginLoader($classLoader, null, CoreKernel::getConnection());

        define('PROJECT_ROOT', $projectRoot);

        $this->kernel = new class($this->appEnv, true, $pluginLoader, 'cache-id') extends CoreKernel {
            public function getProjectDir(): string
            {
                return PROJECT_ROOT;
            }

            protected function buildContainer(): ContainerBuilder
            {
                $container =  parent::buildContainer();

                foreach ($container->getDefinitions() as $definition) {
                    $definition->setPublic(true);
                }

                return $container;
            }
        };

        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
    }

    public function getAvailableVariables(): array
    {
        return [
            'kernel' => $this->kernel,
            'container' => $this->container,
            'definitions' => $this->container->get(DefinitionInstanceRegistry::class),
        ];
    }

    public function contextMenu()
    {
        return [
            Label::create('Detected Shopware ' . $this->version. ', ' . 'APP_ENV='. $this->appEnv . ', APP_DEBUG=1'),

            Submenu::create('Snippets', [
                SetCode::create('Fetch all products', <<<'PHP'
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;

$products = $container->get('product.repository')->search(new Criteria(), Context::createDefaultContext());
$products->first();
PHP),
            ]),

            OpenURL::create('Documentation', 'https://developer.shopware.com'),
        ];
    }
}