<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Config;
use Jtbc\Hook;
use Jtbc\Logger;
use Jtbc\HTTP\Request;
use Jtbc\HTTP\Response;
use Jtbc\Cache\FileCache;

class DI
{
  public const ALIAS = [
    ['name' => 'cache', 'class' => FileCache::class, 'mode' => 'multiple'],
    ['name' => 'hook', 'class' => Hook::class, 'mode' => 'single'],
    ['name' => 'logger', 'class' => Logger::class, 'mode' => 'single'],
    ['name' => 'request', 'class' => Request::class, 'mode' => 'single'],
    ['name' => 'response', 'class' => Response::class, 'mode' => 'single'],
  ];
}