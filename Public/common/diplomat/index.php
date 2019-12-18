<?php
namespace Jtbc;

class Diplomat extends Diplomatist {
  public function index()
  {
    return Jtbc::take('index.index');
  }
}