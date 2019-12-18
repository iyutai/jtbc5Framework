<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DAL;
use Jtbc\DB\DBFactory;

class DBDirect
{
  private static $db;

  private static function getDB($argDBLink = null)
  {
    $DBLink = $argDBLink;
    $db = self::$db ?? DBFactory::getInstance($DBLink);
    return $db;
  }

  public static function exec($argSQL, $argDBLink = null)
  {
    $SQL = $argSQL;
    $DBLink = $argDBLink;
    $db = self::getDB($DBLink);
    $result = $db -> exec($SQL);
    return $result;
  }

  public static function select($argSQL, $argDBLink = null)
  {
    $SQL = $argSQL;
    $DBLink = $argDBLink;
    $db = self::getDB($DBLink);
    $result = $db -> fetch($SQL);
    return $result;
  }

  public static function selectAll($argSQL, $argDBLink = null)
  {
    $SQL = $argSQL;
    $DBLink = $argDBLink;
    $db = self::getDB($DBLink);
    $result = $db -> fetchAll($SQL);
    return $result;
  }
}