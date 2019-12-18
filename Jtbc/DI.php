<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use ReflectionClass;
use ReflectionMethod;
use Jtbc\Exception\NotExistException;
use Jtbc\Exception\NotCallableException;

class DI
{
  private $alias = [];
  private $container = [];

  public function make(...$args)
  {
    $instance = null;
    if (is_array($args))
    {
      $name = array_shift($args);
      $class = $this -> alias[$name]['class'] ?? null;
      if (is_null($class)) $class = $name;
      $instance = new $class(...$args);
    }
    return $instance;
  }

  public function autoMake($argName)
  {
    $instance = null;
    $name = $argName;
    $class = $this -> alias[$name]['class'] ?? null;
    if (is_null($class)) $class = $name;
    if (class_exists($class))
    {
      $injectParameters = $this -> inject($class);
      if (is_null($injectParameters)) $instance = new $class();
      else $instance = new $class(...$injectParameters);
    }
    else
    {
      throw new NotExistException('"' . $class . '" not exist', 50404);
    }
    return $instance;
  }

  public function bind($argName, $argClass, $argMode = 'single')
  {
    $name = $argName;
    $class = $argClass;
    $mode = $argMode;
    $this -> alias[$name] = ['class' => $class, 'mode' => $mode];
  }

  public function inject($argClass, $argMethod = null)
  {
    $result = null;
    $class = $argClass;
    $method = $argMethod;
    $reflectionMethod = null;
    if (is_null($method))
    {
      if (class_exists($class))
      {
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass -> getConstructor();
      }
    }
    else if (method_exists($class, $method))
    {
      $reflectionMethod = new ReflectionMethod($class, $method);
    }
    if (!is_null($reflectionMethod))
    {
      $result = [];
      $parameters = $reflectionMethod -> getParameters();
      if (!empty($parameters))
      {
        foreach ($parameters as $parameter)
        {
          $injectValue = null;
          $defaultValue = null;
          if ($parameter -> isDefaultValueAvailable())
          {
            $defaultValue = $parameter -> getDefaultValue();
          }
          $parameterName = $parameter -> getName();
          $parameterType = $parameter -> getType();
          $parameterRequestValue = $this -> request -> get($parameterName) ?? $defaultValue;
          if (is_null($parameterType)) $injectValue = $parameterRequestValue;
          else
          {
            $parameterTypeName = $parameterType -> getName();
            $parameterTypeNameArray = explode('\\', $parameterTypeName);
            $parameterTypeAbbrName = strtolower(array_pop($parameterTypeNameArray));
            if ($parameterTypeName == 'string')
            {
              $injectValue = strval($parameterRequestValue);
            }
            else if ($parameterTypeName == 'int')
            {
              $injectValue = intval($parameterRequestValue);
            }
            else if (array_key_exists($parameterTypeAbbrName, $this -> container))
            {
              $injectValue = $this -> container[$parameterTypeAbbrName];
            }
            else
            {
              $injectValue = $this -> autoMake($parameterTypeName);
            }
          }
          array_push($result, $injectValue);
        }
      }
    }
    return $result;
  }

  public function call($argClass, $argMethod, $argThrowException = true)
  {
    $result = null;
    $class = $argClass;
    $method = $argMethod;
    $throwException = $argThrowException;
    if (method_exists($class, $method) && is_callable([$class, $method]))
    {
      $injectParameters = $this -> inject($class, $method);
      $callParameters = [[$class, $method]];
      if (is_array($injectParameters)) $callParameters = array_merge($callParameters, $injectParameters);
      $result = call_user_func(...$callParameters);
    }
    else
    {
      if ($throwException == true)
      {
        throw new NotCallableException('Not callable', 50403);
      }
    }
    return $result;
  }

  public function __set($argName, $argDefinition)
  {
    $name = $argName;
    $definition = $argDefinition;
    $name = strtolower($name);
    $this -> container[$name] = $definition;
  }

  public function __get($argName)
  {
    $name = $argName;
    $definition = null;
    $name = strtolower($name);
    if (array_key_exists($name, $this -> container))
    {
      $definition = $this -> container[$name];
    }
    else
    {
      $definition = $this -> autoMake($name);
      $aliasMode = $this -> alias[$name]['mode'] ?? null;
      if ($aliasMode == 'single') $this -> container[$name] = $definition;
    }
    return $definition;
  }
}