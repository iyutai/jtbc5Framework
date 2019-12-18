<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DAL;

class SQLConditionBuilder
{
  public $pocket = [];
  public $additionalSQL = null;

  private function getBrickInstance($argName)
  {
    $name = $argName;
    $instance = new SQLConditionBrick($this, $name);
    return $instance;
  }

  public function and($argBrick)
  {
    $brick = $argBrick;
    $this -> set($brick, 'and');
  }

  public function or($argBrick)
  {
    $brick = $argBrick;
    $this -> set($brick, 'or');
  }

  public function set($argBrick, $argAndOr = 'and')
  {
    $brick = $argBrick;
    $andOr = $argAndOr;
    if (!is_null($brick)) $this -> pocket[] = ['brick' => $brick, 'andor' => $andOr];
    return $this;
  }

  public function setAdditionalSQL($argAdditionalSQL)
  {
    $this -> additionalSQL = $argAdditionalSQL;
    return $this;
  }

  public function isEmptyCondition()
  {
    $bool = true;
    if (!empty($this -> pocket)) $bool = false;
    return $bool;
  }

  public function __set($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> getBrickInstance($name) -> equal($value);
  }

  public function __get($argName)
  {
    $name = $argName;
    return $this -> getBrickInstance($name);
  }
}