<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Router;
use Jtbc\Path;
use Jtbc\Diplomat;

class AutoRouter extends ManualRouter
{
  public function autoRun()
  {
    $autoRouterMatch = false;
    $request = $this -> di -> request;
    $pathInfo = $request -> server('PATH_INFO');
    $scriptPath = Path::getScriptPathByPathInfo($pathInfo);
    $scriptDir = pathinfo(substr($scriptPath, 1), PATHINFO_DIRNAME);
    if (is_dir($scriptDir))
    {
      chdir($scriptDir);
      $diplomatPath = 'common/diplomat/' . basename($scriptPath);
      if (is_file($diplomatPath))
      {
        $autoRouterMatch = true;
        require_once($diplomatPath);
        $diplomat = new Diplomat();
        $this -> output($diplomat -> getResult());
      }
    }
    if ($autoRouterMatch == false) $this -> manualRun();
  }

  public static function run()
  {
    $instance = new self();
    $instance -> autoRun();
  }
}