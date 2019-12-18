<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DB;
use Jtbc\DB\MySQL as DataBase;
use Jtbc\Exception\DBException;
use Config\DB\MySQL as Config;

class DBFactory
{
  public static $dbW = null;
  public static $dbR = null;

  public static function getInstance($argDbLink = null)
  {
    $db = null;
    $dbLink = $argDbLink;
    if (!is_null(self::$dbW)) $db = self::$dbW;
    else
    {
      $db = new DataBase(Config::HOST, Config::DATABASE, Config::USERNAME, Config::PASSWORD);
      if ($db -> errCode == 0) self::$dbW = $db;
      else
      {
        $db = null;
        throw new DBException($db -> errMessage, $db -> errCode);
      }
    }
    return $db;
  }
}