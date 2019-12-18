<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Date
{
  public static function format($argDateTime, $argType = null)
  {
    $result = null;
    $dateTime = $argDateTime;
    $type = $argType;
    $time = strtotime($dateTime);
    switch($type)
    {
      case -7:
        $result = date('w', $time);
        break;
      case -6:
        $result = date('s', $time);
        break;
      case -5:
        $result = date('i', $time);
        break;
      case -4:
        $result = date('H', $time);
        break;
      case -3:
        $result = date('d', $time);
        break;
      case -2:
        $result = date('m', $time);
        break;
      case -1:
        $result = date('Y', $time);
        break;
      case 0:
        $result = date('Ymd', $time);
        break;
      case 1:
        $result = date('Y-m-d', $time);
        break;
      case 2:
        $result = date('Y.m.d', $time);
        break;
      case 3:
        $result = date('Y/m/d', $time);
        break;
      case 10:
        $result = date('His', $time);
        break;
      case 11:
        $result = date('H:i:s', $time);
        break;
      case 20:
        $result = date('YmdHis', $time);
        break;
      case 21:
        $result = date('Y-m-d H:i:s', $time);
        break;
      case 30:
        $result = date('md', $time);
        break;
      case 31:
        $result = date('m-d', $time);
        break;
      case 32:
        $result = date('m.d', $time);
        break;
      case 33:
        $result = date('m/d', $time);
        break;
      case 40:
        $result = date('Hi', $time);
        break;
      case 41:
        $result = date('H:i', $time);
        break;
      default:
        $result = date('Y-m-d H:i:s', $time);
        break;
    }
    return $result;
  }

  public static function now()
  {
    return date('Y-m-d H:i:s', time());
  }

  public static function today()
  {
    return date('Y-m-d', time());
  }

  public static function tomorrow()
  {
    return date('Y-m-d', time() + 24 * 60 * 60);
  }

  public static function theDayAfterTomorrow()
  {
    return date('Y-m-d', time() + 2 * 24 * 60 * 60);
  }
}