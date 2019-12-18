<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Hook
{
  private $hooks = [];

  public function add($argName, $argFunction)
  {
    $name = $argName;
    $function = $argFunction;
    $hooks = $this -> hooks;
    if (!array_key_exists($name, $hooks))
    {
      $this -> hooks[$name] = $function;
    }
    else
    {
      $hook = $hooks[$name];
      if (is_object($hook))
      {
        $group = [];
        array_unshift($group, $hook);
        array_unshift($group, $function);
        $this -> hooks[$name] = $group;
      }
      else if (is_array($hook))
      {
        array_unshift($hook, $function);
        $this -> hooks[$name] = $hook;
      }
      else $this -> hooks[$name] = $function;
    }
    return $this;
  }

  public function exists($argName)
  {
    $bool = false;
    $name = $argName;
    $hooks = $this -> hooks;
    if (array_key_exists($name, $hooks)) $bool = true;
    return $bool;
  }

  public function remove($argName)
  {
    $bool = false;
    $name = $argName;
    if (array_key_exists($name, $this -> hooks))
    {
      unset($this -> hooks[$name]);
      $bool = true;
    }
    return $bool;
  }

  public function trigger(...$args)
  {
    $result = null;
    $hooks = $this -> hooks;
    if (!empty($args))
    {
      $name = array_shift($args);
      if (array_key_exists($name, $hooks))
      {
        $result = [];
        $hook = $hooks[$name];
        $trigger = function($argHook) use ($args, &$result)
        {
          $myHook = $argHook;
          $result[] = call_user_func_array($myHook, $args);
        };
        if (is_object($hook)) $trigger($hook);
        else if (is_array($hook))
        {
          foreach ($hook as $key => $val)
          {
            if (is_object($val)) $trigger($val);
          }
        }
      }
    }
    return $result;
  }

  public function __get($argName)
  {
    $name = $argName;
    $class = new class($name, $this) {
      private $instance = null;
      private $name = null;

      public function exists()
      {
        return $this -> instance -> exists($this -> name);
      }

      public function remove()
      {
        return $this -> instance -> remove($name);
      }

      public function trigger(...$args)
      {
        array_unshift($args, $this -> name);
        return $this -> instance -> trigger(...$args);
      }

      public function __construct($argName, $argInstance)
      {
        $this -> name = $argName;
        $this -> instance = $argInstance;
      }
    };
    return $class;
  }

  public function __set($argName, $argFunction)
  {
    $name = $argName;
    $function = $argFunction;
    $this -> add($name, $function);
  }
}