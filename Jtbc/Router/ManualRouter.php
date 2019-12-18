<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Router;
use Jtbc\Util;
use Jtbc\Route;
use Jtbc\Router;
use Config\Route as Config;

class ManualRouter extends Router
{
  public function manualRun()
  {
    $result = null;
    $route = new Route();
    Config::addRoute($route);
    $request = $this -> di -> request;
    $response = $this -> di -> response;
    $routeResult = $route -> handle($request -> method, $request -> server('PATH_INFO'));
    if (is_null($routeResult)) $response -> setStatusCode(404);
    else
    {
      $result = call_user_func($routeResult['callback'], ...$routeResult['args']);
    }
    $this -> output($result);
  }

  public static function run()
  {
    $instance = new self();
    $instance -> manualRun();
  }
}