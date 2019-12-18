<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

interface Cache
{
  public function exist($argName);
  public function get($argName);
  public function put($argName, $argData, $argExpire = null);
  public function remove($argName = '');
  public function removeByKey($argKey, $argMode = 0);
}