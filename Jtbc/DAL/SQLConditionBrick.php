<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DAL;
use Jtbc\Exception\NotCallableException;

class SQLConditionBrick
{
  private $builder = null;
  public $name = null;
  public $value = null;
  public $condition = null;
  public $andOr = 'and';

  private function updateTobuilder()
  {
    $object = (object)[];
    $object -> name = $this -> name;
    $object -> value = $this -> value;
    $object -> condition = $this -> condition;
    return $this -> builder -> set($object, $this -> andOr);
  }

  public function fuzzyLike(...$args)
  {
    foreach ($args as $arg)
    {
      $this -> like('%' . $arg . '%');
    }
  }

  public function __call($argName, $args)
  {
    $name = $argName;
    $methodToConditionMap = ['equal', 'unEqual', 'lessThan', 'moreThan', 'min', 'max', 'in', 'notIn', 'like', 'notLike', 'between', 'notBetween'];
    if (in_array($name, $methodToConditionMap))
    {
      $value = null;
      if (is_array($args))
      {
        $value = array_shift($args);
        if ($name == 'between' || $name == 'notBetween')
        {
          if (!is_array($value) && count($args) == 1)
          {
            $nextValue = array_shift($args);
            $value = [$value, $nextValue];
          }
        }
      }
      $this -> value = $value;
      $this -> condition = $name;
      $this -> updateTobuilder();
    }
    else
    {
      throw new NotCallableException('Not callable', 50403);
    }
    return $this;
  }

  public function __get($argName)
  {
    $name = $argName;
    $result = $this;
    if ($name == 'or') $this -> andOr = 'or';
    else if ($name == 'and') $this -> andOr = 'and';
    else
    {
      $result = null;
    }
    return $result;
  }

  public function __construct(SQLConditionBuilder $argBuilder, $argName)
  {
    $this -> builder = $argBuilder;
    $this -> name = $argName;
  }
}