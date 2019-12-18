<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Exception;

class Handler
{
  public static function output($argException)
  {
    $exception = $argException;
    $html = '<!DOCTYPE html><html><head><title>Fatal Error</title></head><body>';
    $html .= '<h1>Fatal Error</h1>';
    $html .= '<p><b>Code:</b> ' . $exception -> getCode() . '</p>';
    $html .= '<p><b>Message:</b> ' . htmlspecialchars($exception -> getMessage()) . '</p>';
    $html .= '<p><b>File:</b> ' . htmlspecialchars($exception -> getFile()) . ' &nbsp; <b>Line:</b> ' . htmlspecialchars($exception -> getLine()) . '</p>';
    $html .= '<p><b>Trace:</b></p>';
    $html .= '<ul>';
    $traceString = $exception -> getTraceAsString();
    $traceArray = explode(chr(10), $traceString);
    if (is_array($traceArray))
    {
      foreach ($traceArray as $val)
      {
        $html .= '<li>' . htmlspecialchars($val) . '</li>';
      }
    }
    $html .= '</ul>';
    $html .= '</body></html>';
    print($html);
  }
}