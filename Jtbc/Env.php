<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use Config\Env as Config;

class Env
{
  private static $param = [];
  private static $language = null;
  private static $template = null;
  private static $majorGenre = null;

  public static function getParam($argName)
  {
    $param = null;
    $name = $argName;
    if (array_key_exists($name, self::$param)) $param = self::$param[$name];
    return $param;
  }

  public static function setParam($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    self::$param[$name] = $value;
    return $value;
  }

  public static function setParams(array $argParams, $argPrefix = '')
  {
    $params = $argParams;
    $prefix = $argPrefix;
    foreach ($params as $key => $val)
    {
      self::setParam($prefix . $key, $val);
    }
  }

  public static function getLanguage()
  {
    $language = self::$language ?: Config::LANGUAGE;
    return $language;
  }

  public static function setLanguage($argLanguage)
  {
    $language = $argLanguage;
    self::$language = $language;
  }

  public static function getTemplate()
  {
    $template = self::$template ?: Config::TEMPLATE;
    return $template;
  }

  public static function setTemplate($argTemplate)
  {
    $template = $argTemplate;
    self::$template = $template;
  }

  public static function getMajorGenre()
  {
    return self::$majorGenre;
  }

  public static function setMajorGenre($argMajorGenre)
  {
    $majorGenre = $argMajorGenre;
    self::$majorGenre = $majorGenre;
  }

  public static function getVer()
  {
    return Config::VER;
  }
}