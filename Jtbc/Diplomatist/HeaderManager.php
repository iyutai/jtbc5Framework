<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Diplomatist;
use Config\Diplomatist\HeaderManager as Config;

class HeaderManager
{
  public static function handle(callable $handler, $diplomat)
  {
    $result = function() use ($handler, $diplomat)
    {
      $result = $handler();
      $request = $diplomat -> di -> request;
      $response = $diplomat -> di -> response;
      $pageCharset = $diplomat -> getParam('page_charset') ?? Config::CHARSET;
      $pageNoCache = $diplomat -> getParam('page_no_cache') ?? Config::HEADER_NO_CACHE;
      $pageAllowOrigin = $diplomat -> getParam('page_allow_origin') ?? Config::ALLOW_ORIGIN;
      $pageAllowHeaders = $diplomat -> getParam('page_allow_headers') ?? Config::ALLOW_HEADERS;
      $pageAllowCredentials = $diplomat -> getParam('page_allow_credentials') ?? Config::ALLOW_CREDENTIALS;
      $pageContentType = $diplomat -> getParam('page_content_type') ?? 'text/html';
      $contentTypeWithCharset = Config::CONTENT_TYPE_WIDTH_CHARSET ?? [];
      if ($pageNoCache === true)
      {
        $response -> header -> set('Pragma', 'no-cache');
        $response -> header -> set('Cache-Control', 'no-cache, must-revalidate');
      }
      if (is_array($pageAllowOrigin))
      {
        $httpOrigin = $request -> header('origin');
        if (in_array($httpOrigin, $pageAllowOrigin))
        {
          $response -> header -> set('Access-Control-Allow-Origin', $httpOrigin);
          if ($pageAllowCredentials === true) $response -> header -> set('Access-Control-Allow-Credentials', 'true');
        }
      }
      if (is_array($pageAllowHeaders))
      {
        $response -> header -> set('Access-Control-Allow-Headers', implode(',', $pageAllowHeaders));
      }
      if (!in_array($pageContentType, $contentTypeWithCharset))
      {
        $response -> header -> set('Content-Type', $pageContentType);
      }
      else
      {
        $response -> header -> set('Content-Type', $pageContentType . '; charset=' . $pageCharset);
      }
      return $result;
    };
    return $result;
  }
}