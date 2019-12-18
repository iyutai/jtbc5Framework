<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\HTTP;
use Jtbc\Request as RequestInterface;

class Request implements RequestInterface
{
  private $param = [];

  private function source($argType, $argName = null)
  {
    $result = null;
    $type = $argType;
    $name = $argName;
    $source = [];
    switch($type)
    {
      case 'get':
        $source = $this -> source -> get;
        break;
      case 'post':
        $source = $this -> source -> post;
        break;
      case 'files':
        $source = $this -> source -> files;
        break;
      case 'server':
        $source = $this -> source -> server;
        break;
    }
    if (is_null($name)) $result = $source;
    else
    {
      if (array_key_exists($name, $source))
      {
        $result = $source[$name];
      }
    }
    return $result;
  }

  public function get($argName = null)
  {
    return $this -> source('get', $argName);
  }

  public function post($argName = null)
  {
    return $this -> source('post', $argName);
  }

  public function files($argName = null)
  {
    return $this -> source('files', $argName);
  }

  public function server($argName = null)
  {
    return $this -> source('server', $argName);
  }

  public function header($argName)
  {
    $name = $argName;
    $result = $this -> server('HTTP_' . strtoupper($name));
    return $result;
  }

  public function isHTTPS()
  {
    $bool = false;
    if ($this -> server('HTTPS') == 'on' || $this -> server('HTTP_X_FORWARDED_PROTO') == 'https' || $this -> server('HTTP_X_CLIENT_SCHEME') == 'https') $bool = true;
    return $bool;
  }

  public function getIPAddress()
  {
    return $this -> server('HTTP_X_FORWARDED_FOR') ?? $this -> server('HTTP_CLIENT_IP') ?? $this -> server('REMOTE_ADDR');
  }

  public function __get($argName)
  {
    $result = null;
    $name = $argName;
    if ($name == 'cookie')
    {
      if (!isset($this -> param['cookie']))
      {
        $this -> param['cookie'] = new class($this) {
          private $parent;

          public function get($argName, $argChildName = null)
          {
            $result = null;
            $name = $argName;
            $childName = $argChildName;
            $source = $this -> parent -> source -> cookie;
            if (array_key_exists($name, $source))
            {
              $tempResult = $source[$name];
              if (is_null($childName)) $result = $tempResult;
              else
              {
                if (is_array($tempResult))
                {
                  if (array_key_exists($childName, $tempResult)) $result = $tempResult[$childName];
                }
              }
            }
            return $result;
          }

          public function __construct($argParent)
          {
            $this -> parent = $argParent;
          }
        };
      }
      return $this -> param['cookie'];
    }
    else if ($name == 'input')
    {
      $result = file_get_contents('php://input');
    }
    else if ($name == 'method')
    {
      $result = $this -> server('REQUEST_METHOD');
    }
    else if ($name == 'source')
    {
      if (!isset($this -> param['source']))
      {
        $this -> param['source'] = (object)[
          'get' => $_GET,
          'post' => $_POST,
          'files' => $_FILES,
          'server' => $_SERVER,
          'cookie' => $_COOKIE,
        ];
      }
      return $this -> param['source'];
    }
    return $result;
  }
}