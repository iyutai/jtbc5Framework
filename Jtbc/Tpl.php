<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Tpl
{
  private $template = '';
  private $placeHolder = '<!--JTBC_TEMP_PLACEHOLDER-->';
  private $loopBody = null;
  private $loopGroup = '';

  public function assign(array $argVars, callable $argLoopCallBack = null)
  {
    $vars = $argVars;
    $loopCallBack = $argLoopCallBack;
    foreach ($vars as $item)
    {
      $this -> insertLoopGroupLine($item, $loopCallBack);
    }
    return $this;
  }

  public function getLoopBody($argIdentifier = '{@}')
  {
    $loopBody = '';
    $identifier = $argIdentifier;
    if (!is_null($this -> loopBody)) $loopBody = $this -> loopBody;
    else
    {
      $template = $this -> template;
      if (substr_count($template, $identifier) == 2)
      {
        $tempArray = explode($identifier, $template);
        $this -> loopBody = $loopBody = $tempArray[1];
        $this -> template = $tempArray[0] . $this -> placeHolder . $tempArray[2];
      }
    }
    return $loopBody;
  }

  public function getResult()
  {
    $result = str_replace($this -> placeHolder, $this -> loopGroup, $this -> template);
    return $result;
  }

  public function insertLoopGroupLine($argValue, callable $argLoopCallBack = null)
  {
    $value = $argValue;
    $loopCallBack = $argLoopCallBack;
    $loopGroupLine = '';
    if (is_string($value) || is_numeric($value)) $loopGroupLine = $value;
    else if (is_array($value))
    {
      $loopBody = $this -> getLoopBody();
      foreach ($value as $key => $val)
      {
        $loopBody = str_replace('{$' . $key . '}', Encode::htmlEncode($val), $loopBody);
      }
      $loopGroupLine = $loopBody;
    }
    if (is_callable($loopCallBack))
    {
      $loopGroupLine = $loopCallBack($loopGroupLine);
    }
    $this -> loopGroup .= $loopGroupLine;
    return $this;
  }

  public function __construct($argTemplate = '', array $argVariables = [])
  {
    $template = $argTemplate;
    $variables = $argVariables;
    foreach ($variables as $key => $val)
    {
      $template = str_replace('{$' . $key . '}', Encode::htmlEncode($val), $template);
    }
    $this -> template = $template;
  }
}