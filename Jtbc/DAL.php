<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use Jtbc\DB\DBFactory;
use Jtbc\DAL\SQLBuilder;
use Jtbc\Exception\EmptyException;

class DAL
{
  private $db;
  private $table;
  private $SQLBuilder;
  public $lastInsertId;

  public function getRsCount()
  {
    $rsCount = -1;
    $rs = $this -> select('count(*) as rs_count');
    if (is_array($rs)) $rsCount = intval($rs['rs_count']);
    return $rsCount;
  }

  public function select($argField = '*')
  {
    $result = null;
    $field = $argField;
    $db = $this -> db;
    $selectSQL = $this -> SQLBuilder -> getSelectSQL($field);
    $result = $db -> fetch($selectSQL);
    return $result;
  }

  public function selectAll($argField = '*')
  {
    $result = null;
    $field = $argField;
    $db = $this -> db;
    $selectSQL = $this -> SQLBuilder -> getSelectSQL($field);
    $result = $db -> fetchAll($selectSQL);
    return $result;
  }

  public function insert($argSource)
  {
    $result = false;
    $source = $argSource;
    $db = $this -> db;
    $insertSQL = $this -> SQLBuilder -> getInsertSQL($source);
    if (!Validation::isEmpty($insertSQL))
    {
      $result = $db -> exec($insertSQL);
      $this -> lastInsertId = $db -> lastInsertId;
    }
    return $result;
  }

  public function update($argSource)
  {
    $result = false;
    $source = $argSource;
    $db = $this -> db;
    $updateSQL = $this -> SQLBuilder -> getUpdateSQL($source);
    if (!Validation::isEmpty($updateSQL)) $result = $db -> exec($updateSQL);
    return $result;
  }

  public function delete()
  {
    $result = false;
    $db = $this -> db;
    $updateSQL = $this -> SQLBuilder -> getUpdateSQL(['deleted' => 1]);
    if (!Validation::isEmpty($updateSQL)) $result = $db -> exec($updateSQL);
    return $result;
  }

  public function __call($argName, $args) 
  {
    $result = null;
    $name = $argName;
    if (is_callable([$this -> SQLBuilder, $name]))
    {
      $result = call_user_func_array([$this -> SQLBuilder, $name], $args);
    }
    return $result;
  }

  public function __get($argName)
  {
    $name = $argName;
    return $this -> SQLBuilder -> {$name};
  }

  public function __set($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> SQLBuilder -> {$name} = $value;
  }

  public function __construct($argTable = null, $argDBLink = null, $argAutoFilter = true)
  {
    $table = $argTable;
    $DBLink = $argDBLink;
    $autoFilter = $argAutoFilter;
    $this -> db = DBFactory::getInstance($DBLink);
    if (is_null($table)) $table = Jtbc::take('config.db_table', 'cfg');
    if (Validation::isEmpty($table))
    {
      throw new EmptyException('table can not be empty', 50204);
    }
    else
    {
      $this -> table = $table;
      $this -> SQLBuilder = new SQLBuilder($this -> db, $this -> table, $autoFilter);
    }
  }
}