<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Validation
{
  public static function isDate($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      $dateArray = explode('-', $str);
      if (count($dateArray) == 3)
      {
        list($year, $month, $day) = $dateArray;
        if (is_numeric($year) && is_numeric($month) && is_numeric($day))
        {
          $bool = checkdate($month, $day, $year);
        }
      }
    }
    return $bool;
  }

  public static function isDateTime($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      $standardDate = date('Y-m-d H:i:s', strtotime($str));
      if ($standardDate == $str) $bool = true;
    }
    return $bool;
  }

  public static function isEmpty($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (trim($str) == '') $bool = true;
    return $bool;
  }

  public static function isEmail($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $str)) $bool = true;
    }
    return $bool;
  }

  public static function isIDCard($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      if (preg_match('/(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $str))
      {
        $checkSum = 0;
        $cardBase = substr($str, 0, 17);
        $codeFactor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $verifyNumberList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        for ($ti = 0; $ti < strlen($cardBase); $ti ++)
        {
          $checkSum += substr($cardBase, $ti, 1) * $codeFactor[$ti];
        }
        $verifyNumber = $verifyNumberList[$checkSum % 11];
        if (strtoupper(substr($str, 17, 1)) == $verifyNumber) $bool = true;
      }
    }
    return $bool;
  }

  public static function isIntSeries($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (is_int($str)) $bool = true;
    else if (is_string($str))
    {
      $allMatch = true;
      $strArray = explode(',', $str);
      foreach($strArray as $val)
      {
        if (!(is_numeric($val) && strval(intval($val)) == $val)) $allMatch = false;
      }
      if ($allMatch == true) $bool = true;
    }
    return $bool;
  }

  public static function isMobile($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      if (preg_match('/^1\d{10}$/', $str)) $bool = true;
    }
    return $bool;
  }

  public static function isNumber($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      if (preg_match('/^[0-9]*$/', $str)) $bool = true;
    }
    return $bool;
  }

  public static function isNatural($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (!self::isEmpty($str))
    {
      if (preg_match('/^[a-zA-Z0-9_-]+$/', $str)) $bool = true;
    }
    return $bool;
  }

  public static function isURL($argStr)
  {
    $bool = false;
    $str = $argStr;
    if (filter_var($str, FILTER_VALIDATE_URL) !== false) $bool = true;
    return $bool;
  }
}