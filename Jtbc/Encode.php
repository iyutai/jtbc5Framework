<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Encode
{
  public static function htmlEncode($argString, $argMode = 1)
  {
    $string = $argString;
    $mode = $argMode;
    if (!Validation::isEmpty($string))
    {
      if ($mode == 1)
      {
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace('\'', '&apos;', $string);
      }
      $string = str_replace('$', '&#36;', $string);
      $string = str_replace('.', '&#46;', $string);
      $string = str_replace('@', '&#64;', $string);
    }
    return $string;
  }
}