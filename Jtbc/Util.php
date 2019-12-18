<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Util
{
  public static function getJsonParam($argJson, $argName)
  {
    $result = null;
    $json = $argJson;
    $name = $argName;
    $jsonArray = json_decode($json, true);
    if (is_array($jsonArray))
    {
      $nameList = explode('->', $name);
      $sourceArray = $jsonArray;
      foreach ($nameList as $currentName)
      {
        $result = null;
        if (is_array($sourceArray) && array_key_exists($currentName, $sourceArray))
        {
          $result = $sourceArray = $sourceArray[$currentName];
        }
      }
    }
    return $result;
  }

  public static function getLRStr($argString, $argSpStr, $argType)
  {
    $tmpstr = '';
    $string = $argString;
    $spStr = $argSpStr;
    $type = $argType;
    if (Validation::isEmpty($spStr) || strpos($string, $spStr) === false) $tmpstr = $string;
    else
    {
      $tempArray = explode($spStr, $string);
      switch($type)
      {
        case 'left':
          $tempArray = array_slice($tempArray, 0, 1);
          break;
        case 'leftr':
          array_pop($tempArray);
          break;
        case 'right':
          $tempArray = array_slice($tempArray, -1, 1);
          break;
        case 'rightr':
          array_shift($tempArray);
          break;
      }
      $tmpstr = implode($spStr, $tempArray);
    }
    return $tmpstr;
  }

  public static function getNum($argNumber, $argDefault = 0)
  {
    $num = 0;
    $number = $argNumber;
    $default = $argDefault;
    if (is_numeric($number))
    {
      if (is_numeric(strpos($number, '.'))) $num = doubleval($number);
      else $num = intval($number);
    }
    else $num = $default;
    return $num;
  }

  public static function getRandomString($argLength = 16, $argMode = null)
  {
    $tmpstr = '';
    $length = self::getNum($argLength, 0);
    $mode = $argMode;
    switch($mode)
    {
      case 'number':
        $chars = '1234567890';
        break;
      default:
        $chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
        break;
    }
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i ++)
    {
      $tmpstr .= $chars[rand(0, $max)];
    }
    return $tmpstr;
  }
}