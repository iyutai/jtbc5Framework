<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DI;
use Jtbc\DI;
use Config\DI as Config;

class DIFactory
{
  private static $di;

  public static function getInstance()
  {
    $di = null;
    if (!is_null(self::$di)) $di = self::$di;
    else
    {
      $di = self::$di = new DI();
      foreach (Config::ALIAS as $item)
      {
        $di -> bind($item['name'], $item['class'], $item['mode']);
      }
    }
    return $di;
  }
}