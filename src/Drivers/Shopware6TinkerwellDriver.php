<?php

use Composer\InstalledVersions;
use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\ComposerPluginLoader;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Production\Kernel;
use Symfony\Component\Dotenv\Dotenv;

class Shopware6TinkerwellDriver extends TinkerwellDriver
{
    protected $container;
    protected $kernel;

    /**
     * @var string
     */
    protected $version;

    public function canBootstrap($projectPath): bool
    {
        return file_exists($projectPath . '/bin/build-storefront.sh') &&
            file_exists($projectPath . '/install.lock');
    }

    public function bootstrap($projectPath)
    {
        $classLoader = require $projectPath . '/vendor/autoload.php';

        $projectRoot = $projectPath;
        if (class_exists(Dotenv::class) && (file_exists($projectRoot . '/.env.local.php') || file_exists($projectRoot . '/.env') || file_exists($projectRoot . '/.env.dist'))) {
            (new Dotenv())->usePutenv()->setProdEnvs(['prod', 'e2e'])->bootEnv($projectRoot . '/.env');
        }

        if (!EnvironmentHelper::hasVariable('PROJECT_ROOT')) {
            $_SERVER['PROJECT_ROOT'] = $projectRoot;
        }

        $pluginLoader = new StaticKernelPluginLoader($classLoader, null);

        $this->Version = InstalledVersions::getVersion('shopware/core') . '@' . InstalledVersions::getReference('shopware/core');

        $pluginLoader = new DbalKernelPluginLoader($classLoader, null, \Shopware\Core\Kernel::getConnection());

        if ($_SERVER['COMPOSER_PLUGIN_LOADER'] ?? $_SERVER['DISABLE_EXTENSIONS'] ?? false) {
            $pluginLoader = new ComposerPluginLoader($classLoader);
        }

        $env = $_SERVER['APP_ENV'] ?? 'prod';
        $debug = $_SERVER['APP_DEBUG'] ?? ($env !== 'prod');

        $this->kernel = new Kernel($env, $debug, $pluginLoader);

        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
    }

    public function getAvailableVariables()
    {
        return [
            'kernel' => $this->kernel,
            'container' => $this->container,
        ];
    }
}