<?php
ob_start();
set_time_limit(1800);
ini_set('display_errors', '1');
//error_reporting(E_ALL ^ E_NOTICE);

spl_autoload_register(function($argClass){
  $class = $argClass;
  $requireFile = null;
  $classPath = str_replace('\\', '/', $class);
  $firstPath = strstr($classPath, '/', true);
  if ($firstPath == 'App' || $firstPath == 'Jtbc' || $firstPath == 'Config')
  {
    $requireFile = __DIR__ . '/../' . $firstPath . '/' . ltrim(substr($classPath, strpos($classPath, '/')), '/') . '.php';
  }
  else if ($firstPath == 'web')
  {
    $folder = '';
    $childFile = ltrim(strstr($classPath, '/'), '/');
    if (is_numeric(strpos($childFile, '/')))
    {
      $folder = substr($childFile, 0, strrpos($childFile, '/'));
      $childFile = ltrim(substr($childFile, strrpos($childFile, '/')), '/');
    }
    $requireFile = __DIR__ . '/../Public/' . $folder . '/library/' . $childFile . '.php';
  }
  else $requireFile = __DIR__ . '/../Vendor/' . $classPath . '.php';
  if (!is_null($requireFile) && is_file($requireFile)) require_once($requireFile);
});

set_exception_handler(['Jtbc\Exception\Handler', 'output']);