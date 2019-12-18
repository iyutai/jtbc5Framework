<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DAL;
use Jtbc\DB;
use Jtbc\DB\DBTools;
use Jtbc\DI\DIFactory;

class SQLBuilder
{
  private $di;
  private $db;
  private $table;
  private $SQLQueryBuilder;

  public function getSelectSQL($argFields = '*')
  {
    $selectSQL = '';
    $fields = $argFields;
    $di = $this -> di;
    $db = $this -> db;
    $table = $this -> table;
    $dbTools = new DBTools($db, $di -> cache);
    if (is_null($fields)) $fields = '*';
    else if (is_array($fields))
    {
      $tempFields = [];
      foreach ($fields as $field)
      {
        if (!$dbTools -> hasField($table, $field))
        {
          $tempFields[] = $field;
        }
        else
        {
          $tempFields[] = DBTools::formatName($field);
        }
      }
      $fields = implode(',', $tempFields);
    }
    $selectSQL = 'select ' . $fields . ' from ' . DBTools::formatName($table) . $this -> SQLQueryBuilder -> getWhere() . $this -> SQLQueryBuilder -> getTail();
    return $selectSQL;
  }

  public function getInsertSQL($argSource)
  {
    $insertSQL = '';
    $source = $argSource;
    $di = $this -> di;
    $db = $this -> db;
    $table = $this -> table;
    $dbTools = new DBTools($db, $di -> cache);
    $tableInfo = $dbTools -> getTableInfo($table);
    if (is_array($source) && is_array($tableInfo))
    {
      $fieldName = [];
      $fieldValue = [];
      $assignResult = [];
      foreach ($tableInfo as $item)
      {
        $tempAssignResult = SQLFieldAssign::assign($item, $source);
        if (!is_null($tempAssignResult)) $assignResult[] = $tempAssignResult;
      }
      foreach ($assignResult as $item)
      {
        $fieldName[] = $item[0];
        $fieldValue[] = $item[1];
      }
      if (!empty($fieldName) && !empty($fieldValue))
      {
        $insertSQL = 'insert into ' . DBTools::formatName($table) . ' (' . implode(',', $fieldName) . ') values (' . implode(',', $fieldValue) . ')';
      }
    }
    return $insertSQL;
  }

  public function getUpdateSQL($argSource)
  {
    $updateSQL = '';
    $source = $argSource;
    $di = $this -> di;
    $db = $this -> db;
    $table = $this -> table;
    $dbTools = new DBTools($db, $di -> cache);
    $tableInfo = $dbTools -> getTableInfo($table);
    if (is_array($source) && is_array($tableInfo))
    {
      $assignResult = [];
      foreach ($tableInfo as $item)
      {
        $tempAssignResult = SQLFieldAssign::assign($item, $source);
        if (!is_null($tempAssignResult)) $assignResult[] = $tempAssignResult;
      }
      if (!empty($assignResult))
      {
        $updateFieldItem = [];
        foreach ($assignResult as $item)
        {
          $updateFieldItem[] = $item[0] . '=' . $item[1];
        }
        $updateSQL = 'update ' . DBTools::formatName($table) . ' set ' . implode(',', $updateFieldItem) . $this -> SQLQueryBuilder -> getWhere();
      }
    }
    return $updateSQL;
  }

  public function __call($argName, $args) 
  {
    $result = null;
    $name = $argName;
    if (is_callable([$this -> SQLQueryBuilder, $name]))
    {
      $result = call_user_func_array([$this -> SQLQueryBuilder, $name], $args);
    }
    return $result;
  }

  public function __get($argName)
  {
    $name = $argName;
    return $this -> SQLQueryBuilder -> {$name};
  }

  public function __set($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> SQLQueryBuilder -> {$name} = $value;
  }

  public function __construct(DB $argDb, $argTable, $argAutoFilter = true)
  {
    $db = $argDb;
    $table = $argTable;
    $autoFilter = $argAutoFilter;
    $this -> db = $db;
    $this -> table = $table;
    $this -> di = DIFactory::getInstance();
    $this -> SQLQueryBuilder = new SQLQueryBuilder($this -> db, $this -> table, $autoFilter);
  }
}