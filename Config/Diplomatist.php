<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Config;

class Diplomatist
{
  public const MIDDLEWARES = [
    'Jtbc\Diplomatist\IPManager::handle',
    'Jtbc\Diplomatist\HeaderManager::handle',
  ];
}