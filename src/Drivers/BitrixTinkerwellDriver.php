<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class BitrixTinkerwellDriver extends TinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/bitrix/modules/main/include/prolog_before.php');
    }

    public function bootstrap($projectPath)
    {
        $_SERVER["DOCUMENT_ROOT"] = $projectPath;

        define("NO_KEEP_STATISTIC", true);
        define("NOT_CHECK_PERMISSIONS",true);
        define('BX_NO_ACCELERATOR_RESET', false);
        define('CHK_EVENT', true);
        define('BX_WITH_ON_AFTER_EPILOG', true);

        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    }

    public function contextMenu()
    {
        return [
            OpenURL::create('Bitrix Framework', 'https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43'),
        ];
    }
}