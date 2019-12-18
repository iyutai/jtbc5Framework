<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use Jtbc\DI\DIFactory;

abstract class Router
{
  protected $di = null;

  public function output($argBody = '')
  {
    $body = $argBody;
    $response = $this -> di -> response;
    $status = $response -> getStatusCode();
    if ($status != 200 && Validation::isEmpty($body))
    {
      $body = Jtbc::take('global.httpStatus.' . $status, 'tpl');
    }
    $response -> send($body);
  }

  public function __construct()
  {
    $this -> di = DIFactory::getInstance();
  }
}