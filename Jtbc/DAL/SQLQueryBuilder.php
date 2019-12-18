<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DAL;
use Jtbc\DB;
use Jtbc\DB\DBTools;
use Jtbc\DI\DIFactory;
use Jtbc\Exception\NotExistException;

class SQLQueryBuilder extends SQLConditionBuilder
{
  private $di;
  private $db;
  private $table;
  private $groupBy = [];
  private $orderBy = [];
  private $limitStart = null;
  private $limitLength = null;
  private $autoFilter = true;

  public function limit(...$args)
  {
    $start = 0;
    $length = 1;
    $argsCount = count($args);
    if ($argsCount == 1)
    {
      $length = intval($args[0]);
    }
    else if ($argsCount == 2)
    {
      $start = intval($args[0]);
      $length = intval($args[1]);
    }
    if ($start < 0) $start = 0;
    if ($length < 1) $length = 1;
    $this -> limitStart = $start;
    $this -> limitLength = $length;
  }

  public function groupBy($argField)
  {
    $field = $argField;
    $this -> groupBy[] = $field;
    return $this;
  }

  public function orderBy($argField, $argDescOrAsc = 'desc')
  {
    $field = $argField;
    $descOrAsc = $argDescOrAsc;
    if (strtolower($descOrAsc) == 'asc') $descOrAsc = 'asc';
    $this -> orderBy[] = [$field, $descOrAsc];
    return $this;
  }

  public function getWhere()
  {
    $result = ' where 1=1';
    $di = $this -> di;
    $db = $this -> db;
    $table = $this -> table;
    $pocket = $this -> pocket;
    $additionalSQL = $this -> additionalSQL;
    $dbTools = new DBTools($db, $di -> cache);
    if ($dbTools -> hasField($table, 'deleted'))
    {
      $result = ' where `deleted` = 0';
    }
    $formatPocketResult = function($argPocket, $argAdditionalSQL, $argInnerMode = false) use ($dbTools, $table, &$formatPocketResult)
    {
      $pocketResult = '';
      $pocket = $argPocket;
      $additionalSQL = $argAdditionalSQL;
      $innerMode = $argInnerMode;
      if (is_array($pocket))
      {
        $conditionIndex = 0;
        foreach ($pocket as $condition)
        {
          $conditionIndex += 1;
          $andOr = $condition['andor'];
          $conditionBrick = $condition['brick'];
          $conditionBrickResult = '';
          if (($conditionBrick instanceof SQLConditionBuilder))
          {
            $conditionBrickResult = '(' . $formatPocketResult($conditionBrick -> pocket, $conditionBrick -> additionalSQL, true) . ')';
          }
          else if (is_object($conditionBrick))
          {
            $name = $conditionBrick -> name;
            $value = $conditionBrick -> value;
            $condition = $conditionBrick -> condition;
            $fieldInfo = $dbTools -> getFieldInfo($table, $name);
            if (is_array($fieldInfo))
            {
              $fieldType = $fieldInfo['type'];
              $fieldLength = intval($fieldInfo['length']);
              $formatResult = call_user_func_array([SQLQueryAssign::class, $condition], [$value, $fieldType, $fieldLength]);
              $conditionBrickResult = DBTools::formatName($name) . $formatResult;
            }
            else
            {
              throw new NotExistException('"' . $name . '" is not exist', 50404);
            }
          }
          if ($innerMode != true || $conditionIndex != 1) $conditionBrickResult = ' ' . $andOr . ' ' . $conditionBrickResult;
          $pocketResult .= $conditionBrickResult;
        }
      }
      if (!is_null($additionalSQL)) $pocketResult .= $additionalSQL;
      return $pocketResult;
    };
    $result .= $formatPocketResult($pocket, $additionalSQL);
    return $result;
  }

  public function getTail()
  {
    $result = '';
    $di = $this -> di;
    $db = $this -> db;
    $table = $this -> table;
    $dbTools = new DBTools($db, $di -> cache);
    $groupBy = $this -> groupBy;
    $orderBy = $this -> orderBy;
    $limitStart = $this -> limitStart;
    $limitLength = $this -> limitLength;
    if (!empty($groupBy))
    {
      $newGroupBy = [];
      foreach ($groupBy as $field)
      {
        if ($dbTools -> hasField($table, $field))
        {
          $newGroupBy[] = DBTools::formatName($field);
        }
        else
        {
          throw new NotExistException('"' . $field . '" is not exist', 50404);
        }
      }
      $result .= ' group by ' . implode(',', $newGroupBy);
    }
    if (!empty($orderBy))
    {
      $newOrderBy = [];
      foreach ($orderBy as $item)
      {
        $field = $item[0];
        $descOrAsc = $item[1];
        if ($dbTools -> hasField($table, $field))
        {
          $newOrderBy[] = DBTools::formatName($field) . ' ' . $descOrAsc;
        }
        else
        {
          throw new NotExistException('"' . $field . '" is not exist', 50404);
        }
      }
      $result .= ' order by ' . implode(',', $newOrderBy);
    }
    if (!is_null($limitStart) && !is_null($limitLength))
    {
      $result .= ' limit ' . $limitStart . ',' . $limitLength;
    }
    return $result;
  }

  public function __construct(DB $argDb, $argTable, $argAutoFilter = true)
  {
    $this -> db = $argDb;
    $this -> table = $argTable;
    $this -> autoFilter = $argAutoFilter;
    $this -> di = DIFactory::getInstance();
  }
}