<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\DB;
use PDO;
use PDOException;
use Jtbc\DB;

class MySQL implements DB
{
  private $conn;
  private $dbHost;
  private $dbDatabase;
  private $dbUsername;
  private $dbPassword;
  private $dbCharset;
  private $cacheInstance = null;
  public $errCode = 0;
  public $errMessage;
  public $lastInsertId;
  public $queryCount = 0;

  private function init()
  {
    try
    {
      $dsn = 'mysql:host=' . $this -> dbHost;
      if (!empty($this -> dbDatabase)) $dsn .= ';dbname=' . $this -> dbDatabase;
      $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this -> dbCharset,
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION,
      ];
      $this -> conn = new PDO($dsn, $this -> dbUsername, $this -> dbPassword, $options);
    }
    catch (PDOException $e)
    {
      $this -> errCode = 40004;
      $this -> errMessage = $e -> getMessage();
    }
  }

  public function fetch($argSQL)
  {
    $sql = $argSQL;
    $rq = $this -> query($sql);
    $rs = $rq -> fetch(PDO::FETCH_ASSOC);
    return $rs;
  }

  public function fetchAll($argSQL)
  {
    $sql = $argSQL;
    $rq = $this -> query($sql);
    $rsAll = $rq -> fetchAll(PDO::FETCH_ASSOC);
    return $rsAll;
  }

  public function query($argSQL)
  {
    $sql = $argSQL;
    $this -> queryCount += 1;
    $query = $this -> conn -> query($sql);
    return $query;
  }

  public function exec($argSQL)
  {
    $sql = $argSQL;
    $this -> queryCount += 1;
    $exec = $this -> conn -> exec($sql);
    if (substr($sql, 0, 6) == 'insert') $this -> lastInsertId = $this -> conn -> lastInsertId();
    return $exec;
  }

  public function hasTable($argTable)
  {
    $bool = false;
    $table = $argTable;
    if (is_string($table) && trim($table) != '')
    {
      $rs = $this -> fetch('show tables like \'' . addslashes($table) . '\'');
      if (is_array($rs)) $bool = true;
    }
    return $bool;
  }

  public function getTableInfo($argTable)
  {
    $table = $argTable;
    $tableInfo = null;
    if ($this -> hasTable($table))
    {
      $table = str_replace('`', '``', $table);
      $tableInfo = $this -> fetchAll('show full columns from `' . $table . '`');
      foreach ($tableInfo as $i => $item)
      {
        $fieldType = $item['Type'];
        $fieldNewType = $fieldType;
        $fieldLength = null;
        $fieldBracketsPos1 = strpos($fieldType, '(');
        $fieldBracketsPos2 = strpos($fieldType, ')');
        if (is_numeric($fieldBracketsPos1) && is_numeric($fieldBracketsPos2))
        {
          $fieldNewType = substr($fieldType, 0, $fieldBracketsPos1);
          $fieldTempLength = substr($fieldType, $fieldBracketsPos1 + 1, $fieldBracketsPos2 - $fieldBracketsPos1 - 1);
          $fieldLength = is_numeric($fieldTempLength)? intval($fieldTempLength): null;
        }
        $item['Type'] = $fieldNewType;
        $item['Length'] = $fieldLength;
        $tableInfo[$i] = array_change_key_case($item);
      }
    }
    return $tableInfo;
  }

  public function __construct($argHost, $argDatabase, $argUsername, $argPassword, $argCharset = 'utf8mb4')
  {
    $this -> dbHost = $argHost;
    $this -> dbDatabase = $argDatabase;
    $this -> dbUsername = $argUsername;
    $this -> dbPassword = $argPassword;
    $this -> dbCharset = $argCharset;
    $this -> init();
  }
}