<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use ArrayAccess;

class Substance implements ArrayAccess {
  private $body = [];

  public function all()
  {
    return $this -> body;
  }

  public function exists($argName)
  {
    $bool = false;
    $name = $argName;
    if (array_key_exists($name, $this -> body)) $bool = true;
    return $bool;
  }

  public function offsetExists($argName)
  {
    return $this -> exists($argName);
  }

  public function offsetSet($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> body[$name] = $value;
  }

  public function offsetGet($argName)
  {
    $result = null;
    $name = $argName;
    if (array_key_exists($name, $this -> body))
    {
      $result = $this -> body[$name];
    }
    return $result;
  }

  public function offsetUnset($argName)
  {
    $name = $argName;
    unset($this -> body[$name]);
  }

  public function __get($argName)
  {
    $name = $argName;
    return $this -> offsetGet($name);
  }

  public function __set($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> offsetSet($name, $value);
  }

  public function __construct($argBody = null)
  {
    $body = $argBody;
    if (is_array($body)) $this -> body = $body;
    else if (is_string($body)) $body = json_decode($body, true);
  }
}