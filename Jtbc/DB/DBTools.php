<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DB;
use Jtbc\DB;
use Jtbc\Cache;
use Config\DB as Config;

class DBTools
{
  private $db;
  private $cache;

  public static function formatName($argName)
  {
    $name = $argName;
    if (is_string($name))
    {
      $name = '`' . str_replace('`', '``', $name) . '`';
    }
    return $name;
  }

  public function hasField($argTable, $argField)
  {
    $bool = false;
    $table = $argTable;
    $field = $argField;
    $fieldInfo = $this -> getFieldInfo($table, $field);
    if (!is_null($fieldInfo)) $bool = true;
    return $bool;
  }

  public function getFieldInfo($argTable, $argField)
  {
    $table = $argTable;
    $field = $argField;
    $fieldInfo = null;
    $tableInfo = $this -> getTableInfo($table);
    if (!is_null($tableInfo))
    {
      foreach ($tableInfo as $item)
      {
        if ($item['field'] == $field)
        {
          $fieldInfo = $item;
          break;
        }
      }
    }
    return $fieldInfo;
  }

  public function getTableInfo($argTable)
  {
    $table = $argTable;
    $tableInfo = null;
    $cacheName = 'db_structure_' . $table;
    $tableInfo = $this -> cache -> get($cacheName);
    if (empty($tableInfo))
    {
      $tableInfo = $this -> db -> getTableInfo($table);
      if (Config::STRUCTURE_CACHE_MODE == true) $this -> cache -> put($cacheName, $tableInfo);
    }
    return $tableInfo;
  }

  public function __construct(DB $argDb, Cache $argCache)
  {
    $this -> db = $argDb;
    $this -> cache = $argCache;
  }
}