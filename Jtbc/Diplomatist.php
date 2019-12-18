<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use Jtbc\DI\DIFactory;
use Config\Diplomatist as Config;

class Diplomatist
{
  private $param = [];
  private $middleware = null;
  public $di = null;

  private function initParam()
  {
    $request = $this -> di -> request;
    $this -> param['scheme'] = $request -> isHTTPS()? 'https://': 'http://';
    $this -> param['host'] = $request -> server('HTTP_HOST');
    $this -> param['referer'] = $request -> server('HTTP_REFERER');
    $this -> param['ip_address'] = $request -> getIPAddress();
    $this -> param['genre'] = Path::getCurrentGenre();
    $this -> param['uri'] = Path::getScriptPathByPathInfo($request -> server('PATH_INFO'));
    $this -> param['query_string'] = $request -> server('QUERY_STRING');
    $this -> param['url'] = Validation::isEmpty($this -> param['query_string'])? $this -> param['uri']: $this -> param['uri'] . '?' . $this -> param['query_string'];
    $this -> param['visible_url'] = $request -> server('REQUEST_URI');
    $this -> param['full_host'] = $this -> param['scheme'] . $this -> param['host'];
    $this -> param['full_uri'] = $this -> param['full_host'] . $this -> param['uri'];
    $this -> param['full_url'] = $this -> param['full_host'] . $this -> param['url'];
    $this -> param['full_visible_url'] = $this -> param['full_host'] . $this -> param['visible_url'];
    return $this;
  }

  private function initMiddleware()
  {
    $this -> middleware = new Middleware(function(){
      return $this -> getPureResult();
    });
    foreach (Config::MIDDLEWARES as $middleware)
    {
      $this -> middleware -> add($middleware, $this);
    }
    return $this;
  }

  private function getPureResult()
  {
    $result = null;
    $di = $this -> di;
    $request = $di -> request;
    $response = $di -> response;
    $startMethodName = '__start';
    $finishMethodName = '__finish';
    $startResult = $di -> call($this, $startMethodName, false);
    if (is_null($startResult))
    {
      $type = $request -> get('type') ?? $this -> getParam('page_type_index') ?? 'index';
      $classMethods = array_diff(get_class_methods(get_called_class()), get_class_methods(__CLASS__));
      if (!in_array($type, $classMethods)) $response -> setStatusCode(404);
      else
      {
        $result = $di -> call($this, $type, false);
        $finishResult = $di -> call($this, $finishMethodName, false);
        if (!is_null($finishResult)) $result = $finishResult;
      }
    }
    else
    {
      $result = $startResult;
    }
    return $result;
  }

  public function setParam($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $this -> param[$name] = $value;
    return $value;
  }

  public function addParam($argName, $argValue)
  {
    $name = $argName;
    $value = $argValue;
    $currentValue = $this -> getParam($name);
    if (is_null($currentValue)) $currentValue = [];
    else if (!is_array($currentValue))
    {
      $tempValue = [];
      array_push($tempValue, $currentValue);
      $currentValue = $tempValue;
    }
    array_push($currentValue, $value);
    $this -> param[$name] = $currentValue;
    return $currentValue;
  }

  public function getParam($argName)
  {
    $name = $argName;
    $param = $this -> param[$name] ?? null;
    return $param;
  }

  public function getResult()
  {
    $result = null;
    $initMethodName = '__init';
    $initResult = $this -> di -> call($this, $initMethodName, false);
    if (is_null($initResult))
    {
      $result = $this -> middleware -> run();
    }
    else
    {
      $result = $initResult;
    }
    return $result;
  }

  public function __construct()
  {
    $this -> di = DIFactory::getInstance();
    $this -> initParam() -> initMiddleware();
  }
}