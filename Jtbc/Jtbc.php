<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;
use DOMXPath;
use DOMDocument;

class Jtbc
{
  private static $param = [];

  private static function getAbbrTransKey($argCodeName, &$type)
  {
    $codename = $argCodeName;
    if (!Validation::isEmpty($codename))
    {
      if (is_numeric(strpos($codename, '>>')))
      {
        $typeList = ['tpl', 'lng', 'cfg'];
        $newType = Util::getLRStr($codename, '>>', 'left');
        $newCodename = Util::getLRStr($codename, '>>', 'right');
        if (in_array($newType, $typeList))
        {
          $type = $newType;
          $codename = self::getAbbrTransKey($newCodename, $type);
        }
      }
      else
      {
        if (substr($codename, 0, 3) == '../')
        {
          $generation = 0;
          $tempCodename = $codename;
          while(strpos($tempCodename, '../') == 0)
          {
            $generation += 1;
            $tempCodename = substr($tempCodename, 3);
          }
          $parentGenre = Path::getParentGenreByGeneration($generation);
          if (!is_null($parentGenre)) $codename = 'global.' . $parentGenre . ':' . $tempCodename;
        }
        else if (substr($codename, 0, 1) == '.')
        {
          if (substr_count($codename, '.') == 2) $codename = 'global.' . Util::getLRStr($codename, '.', 'rightr');
        }
        else
        {
          $majorGenre = Env::getMajorGenre();
          if (!is_null($majorGenre))
          {
            if (substr($codename, 0, 2) == '::') $codename = 'global.' . $majorGenre . ':' . Util::getLRStr($codename, '::', 'right');
            else if (substr($codename, 0, 2) == ':/') $codename = 'global.' . $majorGenre . '/' . Util::getLRStr($codename, ':/', 'right');
          }
        }
      }
    }
    return $codename;
  }

  private static function getActiveValue($argType)
  {
    $tmpstr = null;
    $key = '';
    $type = $argType;
    switch($type)
    {
      case 'cfg':
        $tmpstr = Env::getLanguage();
        break;
      case 'lng':
        $tmpstr = Env::getLanguage();
        break;
      case 'tpl':
        $tmpstr = Env::getTemplate();
        break;
    }
    return $tmpstr;
  }

  private static function getJtbcDirByType($argType)
  {
    $dir = null;
    $type = $argType;
    switch($type)
    {
      case 'cfg':
        $dir = 'common';
        break;
      case 'lng':
        $dir = 'common/language';
        break;
      case 'tpl':
        $dir = 'common/template';
        break;
      default:
        $dir = 'common';
        break;
    }
    return $dir;
  }

  private static function getJtbcFilePath($argCodeName, $argType)
  {
    $jtbcPath = null;
    $type = $argType;
    $codename = $argCodeName;
    $dir = self::getJtbcDirByType($type);
    $pathStr = Util::getLRStr($codename, '.', 'leftr');
    $fileExtension = '.jtbc';
    if (substr($pathStr, 0, 7) == 'global.')
    {
      $pathStr = substr($pathStr, 7);
      if (is_numeric(strpos($pathStr, ':')))
      {
        $jtbcPath = Util::getLRStr($pathStr, ':', 'left') . '/' . $dir . '/' . Util::getLRStr($pathStr, ':', 'right') . $fileExtension;
      }
      else
      {
        $jtbcPath = $dir . '/' . $pathStr . $fileExtension;
      }
    }
    else
    {
      $genre = Path::getCurrentGenre();
      if (is_numeric(strpos($pathStr, ':')))
      {
        $jtbcPath = Util::getLRStr($pathStr, ':', 'left') . '/' . $dir . '/' . Util::getLRStr($pathStr, ':', 'right') . $fileExtension;
      }
      else
      {
        $jtbcPath = $dir . '/' . $pathStr . $fileExtension;
      }
      if (!Validation::isEmpty($genre)) $jtbcPath = $genre . '/' . $jtbcPath;
    }
    $jtbcPath = Path::getActualRoute($jtbcPath);
    return $jtbcPath;
  }

  private static function getJtbcInfo($argSourcefile, $argKeyword, $argType = null, $argGenreAry = null)
  {
    $type = $argType;
    $keyword = $argKeyword;
    $sourceFile = $argSourcefile;
    $genreAry = $argGenreAry;
    $genre = null;
    $thisGenre = null;
    if (is_array($genreAry))
    {
      if (count($genreAry) == 2)
      {
        $genre = $genreAry[0];
        $thisGenre = $genreAry[1];
      }
    }
    $info = [];
    if (is_file($sourceFile))
    {
      $doc = new DOMDocument();
      $doc -> load($sourceFile);
      $xpath = new DOMXPath($doc);
      $query = '//xml/configure/node';
      $node = $xpath -> query($query) -> item(0) -> nodeValue;
      $query = '//xml/configure/field';
      $field = $xpath -> query($query) -> item(0) -> nodeValue;
      $query = '//xml/configure/base';
      $base = $xpath -> query($query) -> item(0) -> nodeValue;
      $fieldArys = explode(',', $field);
      $fieldLength = count($fieldArys);
      if ($fieldLength >= 2)
      {
        $alias = [];
        if (!in_array($keyword, $fieldArys)) $keyword = $fieldArys[1];
        $query = '//xml/' . $base . '/' . $node;
        $rests = $xpath -> query($query);
        foreach ($rests as $rest)
        {
          $nodeName = $rest -> getElementsByTagName(current($fieldArys)) -> item(0) -> nodeValue;
          $nodeDom = $rest -> getElementsByTagName($keyword);
          if ($nodeDom -> length == 0) $nodeDom = $rest -> getElementsByTagName($fieldArys[1]);
          $nodeDomObj = $nodeDom -> item(0);
          $nodeDomValue = $nodeDomObj -> nodeValue;
          if (!is_null($type) && Validation::isEmpty($nodeDomValue))
          {
            if ($nodeDomObj -> hasAttribute('pointer'))
            {
              $pointer = $nodeDomObj -> getAttribute('pointer');
              if (!is_numeric(strpos($pointer, '.'))) $alias[$nodeName] = $pointer;
              else
              {
                if (!is_null($genre)) $pointer = str_replace('{$>genre}', $genre, $pointer);
                if (!is_null($thisGenre)) $pointer = str_replace('{$>this.genre}', $thisGenre, $pointer);
                $nodeDomValue = self::take($pointer, $type);
              }
            }
          }
          $info[$nodeName] = $nodeDomValue;
        }
        if (!empty($alias))
        {
          foreach ($alias as $key => $val)
          {
            if (array_key_exists($val, $info)) $info[$key] = $info[$val];
          }
        }
      }
    }
    return $info;
  }

  private static function preParse($argString)
  {
    $tmpstr = $argString;
    if (!Validation::isEmpty($tmpstr))
    {
      $pregMatch = [];
      preg_match_all('({\$<(.[^\}]*)})', $tmpstr, $pregMatch, PREG_SET_ORDER);
      foreach ($pregMatch as $item)
      {
        $tmpstr = str_replace($item[0], self::execute($item[1]), $tmpstr);
      }
    }
    return $tmpstr;
  }

  private static function execute($argString, $argEnvParamPrefix = '')
  {
    $result = '';
    $string = $argString;
    $envParamPrefix = $argEnvParamPrefix;
    if (!Validation::isEmpty($string))
    {
      $ns = __NAMESPACE__;
      if (Validation::isEmpty($envParamPrefix) || Validation::isNatural($envParamPrefix))
      {
        $string = preg_replace('(\#(.[^(\)|\,)]*))', $ns . '\Env::getParam(\'' . $envParamPrefix . '${1}\')', $string);
      }
      $string = preg_replace('(\$(.[^(\)|\,)]*))', $ns . '\Diplomat\Diplomat::getParam(\'${1}\')', $string);
      $function = Util::getLRStr($string, '(', 'left');
      if (is_callable($function)) eval('$result = ' . $string . ';');
      else
      {
        $function = $ns . '\\' . $function;
        if (is_callable($function)) eval('$result = ' . $ns . '\\' . $string . ';');
      }
    }
    return $result;
  }

  public static function parse($argString, $argEnvParamPrefix = '')
  {
    $tmpstr = $argString;
    $envParamPrefix = $argEnvParamPrefix;
    if (!Validation::isEmpty($tmpstr))
    {
      $pregMatch = [];
      preg_match_all('({\$=(.[^\}]*)})', $tmpstr, $pregMatch, PREG_SET_ORDER);
      foreach ($pregMatch as $item)
      {
        $tmpstr = str_replace($item[0], self::execute($item[1], $envParamPrefix), $tmpstr);
      }
    }
    return $tmpstr;
  }

  public static function take($argCodeName, $argType = null, $argParse = 0, $argVars = null, $argNodeName = null)
  {
    $result = '';
    $type = $argType;
    $codename = $argCodeName;
    $ns = __NAMESPACE__;
    $parse = Util::getNum($argParse, 0);
    $vars = $argVars;
    $nodeName = $argNodeName ?? '';
    if (is_array($codename))
    {
      $result = [];
      foreach ($codename as $val)
      {
        $result[$val] = self::take($val, $type, $parse, $vars, $nodeName);
      }
    }
    else
    {
      $codename = self::getAbbrTransKey($codename, $type);
      if (is_null($type))
      {
        $type = 'tpl';
        $parse = 1;
      }
      $genre = Path::getCurrentGenre();
      $tthis = Util::getLRStr($codename, '.', 'leftr');
      $thisGenre = is_numeric(strpos($codename, ':'))? Util::getLRStr(Util::getLRStr($codename, ':', 'leftr'), 'global.', 'right'): $genre;
      $filePath = self::getJtbcFilePath($codename, $type);
      $keywords = Util::getLRStr($codename, '.', 'right');
      $activeValue = self::getActiveValue($type);
      if (!Validation::isEmpty($nodeName)) $activeValue = $nodeName;
      $cachedKey = $filePath . ':' . $activeValue;
      $cachedJtbcInfo = self::$param[$cachedKey] ?? null;
      if (!is_array($cachedJtbcInfo))
      {
        $cachedJtbcInfo = self::$param[$cachedKey] = self::getJtbcInfo($filePath, $activeValue, $type, [$genre, $thisGenre]);
      }
      if ($keywords == '*') $result = $cachedJtbcInfo;
      else if (is_numeric(strpos($keywords, ',')))
      {
        $result = [];
        $keywordsAry = explode(',', $keywords);
        foreach($keywordsAry as $val)
        {
          $result[$val] = self::take($tthis . '.' . $val, $type, $parse, $vars, $nodeName);
        }
      }
      else if (array_key_exists($keywords, $cachedJtbcInfo)) $result = $cachedJtbcInfo[$keywords];
      else
      {
        if (is_numeric(strpos($keywords, '->')))
        {
          $realKeyword = Util::getLRStr($keywords, '->', 'left');
          $childKeyword = Util::getLRStr($keywords, '->', 'rightr');
          $resultTemp = $cachedJtbcInfo[$realKeyword];
          $result = Util::getJsonParam($resultTemp, $childKeyword);
        }
      }
      if (is_string($result))
      {
        if ($type == 'tpl')
        {
          $result = str_replace('{$>genre}', $genre, $result);
          $result = str_replace('{$>ns}', $ns . '\\', $result);
          $result = str_replace('{$>this}', $tthis, $result);
          $result = str_replace('{$>this.genre}', $thisGenre, $result);
          $result = self::preParse($result);
        }
        if (is_array($vars))
        {
          foreach ($vars as $key => $val) $result = str_replace('{$' . $key . '}', $val, $result);
        }
        if ($parse == 1) $result = self::parse($result);
      }
    }
    return $result;
  }

  public static function takeAndFormat($argCodeName, $argType, $argTplCodeName)
  {
    $tmpstr = '';
    $type = $argType;
    $codeName = $argCodeName;
    $tplCodeName = $argTplCodeName;
    $xmlArray = self::take($codeName, $type);
    if (!is_array($xmlArray))
    {
      $xmlArray = [Util::getLRStr($codeName, '.', 'right') => $xmlArray];
    }
    if (is_array($xmlArray))
    {
      $assignArray = [];
      foreach ($xmlArray as $key => $val)
      {
        $assignArray[] = ['key' => $key, 'val' => $val];
      }
      $tpl = new Tpl(self::take($tplCodeName, 'tpl'));
      $tpl -> assign($assignArray);
      $tmpstr = self::parse($tpl -> getResult());
    }
    return $tmpstr;
  }

  public static function takeAndAssign($argCodeName, array $argVars, array $argVariables = [], callable $argLoopCallBack = null)
  {
    $codename = $argCodeName;
    $vars = $argVars;
    $variables = $argVariables;
    $loopCallBack = $argLoopCallBack;
    $tmpstr = self::take($codename, 'tpl');
    if (!Validation::isEmpty($tmpstr))
    {
      $tpl = new Tpl($tmpstr, $variables);
      foreach ($vars as $item)
      {
        Env::setParams($item);
        $tpl -> insertLoopGroupLine($item, function($argLoopBody) use ($loopCallBack){
          $loopBody = $argLoopBody;
          if (is_callable($loopCallBack))
          {
            $loopCallBack($loopBody);
          }
          $loopBody = self::parse($loopBody);
          return $loopBody;
        });
      }
      $tmpstr = self::parse($tpl -> getResult());
    }
    return $tmpstr;
  }
}