<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

abstract class ORM
{
  private $pocket = [];
  private $dal = null;
  private $where = null;

  public function get()
  {
    $result = $this -> dal -> select();
    return $result;
  }

  public function getAll()
  {
    $result = $this -> dal -> selectAll();
    return $result;
  }

  public function getCount()
  {
    $result = $this -> dal -> getRsCount();
    return $result;
  }

  public function getById(int $argId)
  {
    $id = $argId;
    $this -> dal -> id = $id;
    $result = $this -> dal -> select();
    return $result;
  }

  public function remove($argCautiousMode = true)
  {
    $result = false;
    $cautiousMode = $argCautiousMode;
    if ($this -> dal -> isEmptyCondition())
    {
      if ($cautiousMode == false)
      {
        $result = $this -> dal -> delete();
      }
    }
    else
    {
      $result = $this -> dal -> delete();
    }
    return $result;
  }

  public function save()
  {
    $result = false;
    $pocket = $this -> pocket;
    if (!empty($pocket))
    {
      if ($this -> dal -> isEmptyCondition())
      {
        $result = $this -> dal -> insert($pocket);
      }
      else
      {
        $result = $this -> dal -> update($pocket);
      }
    }
    return $result;
  }

  public function __call($argName, $args) 
  {
    $result = null;
    $name = $argName;
    switch ($name)
    {
      case 'limit':
        $result = $this -> dal -> limit(...$args);
        break;
      case 'orderBy':
        $result = $this -> dal -> orderBy(...$args);
        break;
      case 'groupBy':
        $result = $this -> dal -> groupBy(...$args);
        break;
      case 'setAdditionalSQL':
        $result = $this -> dal -> setAdditionalSQL(...$args);
        break;
    }
    return $result;
  }

  public function __get($argName)
  {
    $result = null;
    $name = $argName;
    if ($name == 'where')
    {
      $result = $this -> where;
    }
    return $result;
  }

  public function __set($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> pocket[$name] = $value;
  }

  public function __construct(string $argTable = null)
  {
    $table = $argTable;
    $className = Util::getLRStr(get_called_class(), '\\', 'right');
    $classToTable = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $className));
    $dalTable = $table ?: $classToTable;
    $this -> dal = new DAL($dalTable);
    $this -> where = new class($this -> dal) {
      private $dal;

      public function __get($argName)
      {
        $name = $argName;
        return $this -> dal -> {$name};
      }

      public function __set($argName, $argValue)
      {
        $name = $argName;
        $value = $argValue;
        $this -> dal -> {$name} = $value;
      }

      public function __construct($dal)
      {
        $this -> dal = $dal;
      }
    };
  }
}