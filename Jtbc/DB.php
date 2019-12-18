<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

interface DB
{
  public function fetch($argSQL);
  public function fetchAll($argSQL);
  public function query($argSQL);
  public function exec($argSQL);
  public function hasTable($argTable);
  public function getTableInfo($argTable);
}