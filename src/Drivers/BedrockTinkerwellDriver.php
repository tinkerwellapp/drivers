<?php

use Tinkerwell\ContextMenu\Label;

class BedrockTinkerwellDriver extends TinkerwellDriver
{

  public function canBootstrap($projectPath)
  {
    return file_exists($projectPath . '/web/wp/wp-load.php');
  }

  public function bootstrap($projectPath)
  {
    require $projectPath . '/web/wp/wp-load.php';
  }

  public function getAvailableVariables()
  {
    return [
      '__blog' => get_bloginfo()
    ];
  }

  public function contextMenu()
  {
    return [
      Label::create('Detected Bedrock v' . get_bloginfo('version')),
    ];
  }

}
