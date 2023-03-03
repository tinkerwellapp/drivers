<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;
use Tinkerwell\ContextMenu\Separator;

class CraftTinkerwellDriver extends TinkerwellDriver
{
    private $version;

    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/web/index.php') &&
            file_exists($projectPath . '/craft');
    }

    public function bootstrap($projectPath)
    {
        // Set path constants
        define('CRAFT_BASE_PATH', $projectPath);
        define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH.'/vendor');

        // Load Composer's autoloader
        require_once CRAFT_VENDOR_PATH.'/autoload.php';

        // Load dotenv?
        if (class_exists('Dotenv\Dotenv') && file_exists(CRAFT_BASE_PATH.'/.env')) {
            $dotenvMajorVersion = null;

            if ( file_exists(CRAFT_VENDOR_PATH.'/composer/installed.json') ) {
                $installedPackages = json_decode(file_get_contents(CRAFT_VENDOR_PATH.'/composer/installed.json'));
                foreach ($installedPackages->packages as $package) {
                    if ($package->name == 'vlucas/phpdotenv') $dotenvMajorVersion = substr($package->version,1,1);
                }
            }

            switch ($dotenvMajorVersion) {
                case '2':
                    (new Dotenv\Dotenv(CRAFT_BASE_PATH))->load();
                    break;
                case '3':
                    Dotenv\Dotenv::create(CRAFT_BASE_PATH)->load();
                    break;
                default:
                    Dotenv\Dotenv::createImmutable(CRAFT_BASE_PATH)->load();
                    break;
            }
        }

        // Load and run Craft
        define('CRAFT_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');
        $app = require CRAFT_VENDOR_PATH.'/craftcms/cms/bootstrap/console.php';
        $app->bootstrap();

        $this->version = $app->version;
    }

    public function contextMenu()
    {
        return array_merge(parent::contextMenu(), [
            Separator::create(),
            Label::create('Detected Craft v' . $this->version),
            OpenURL::create('Documentation', 'https://docs.craftcms.com'),
        ]);
    }
}