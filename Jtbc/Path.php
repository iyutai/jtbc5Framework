<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc;

class Path
{
  private static $currentRank = null;
  private static $currentGenre = null;

  private static function getRank()
  {
    $rank = null;
    $index = -1;
    $rootFile = 'common/root.jtbc';
    while ($index < 5)
    {
      $index += 1;
      if (is_file($rootFile))
      {
        $rank = $index;
        break;
      }
      else
      {
        $rootFile = '../' . $rootFile;
      }
    }
    return $rank;
  }

  private static function getActualGenre($argRank)
  {
    $genre = '';
    $rank = $argRank;
    $currentPath = realpath(getcwd());
    $usefulArray = [];
    $pathArray = array_reverse(explode(DIRECTORY_SEPARATOR, $currentPath));
    if (is_numeric($rank)) $usefulArray = array_slice($pathArray, 0, $rank);
    if (!empty($usefulArray)) $genre = implode('/', $usefulArray);
    return $genre;
  }

  public static function getActualRoute($argRouteStr = '')
  {
    $routeStr = $argRouteStr;
    $route = str_repeat('../', self::getCurrentRank()) . $routeStr;
    return $route;
  }

  public static function getCurrentGenre()
  {
    $currentGenre = self::$currentGenre;
    if (is_null($currentGenre))
    {
      $currentGenre = self::$currentGenre = self::getActualGenre(self::getCurrentRank());
    }
    return $currentGenre;
  }

  public static function getCurrentRank()
  {
    $currentRank = self::$currentRank;
    if (is_null($currentRank))
    {
      $currentRank = self::$currentRank = self::getRank();
    }
    return $currentRank;
  }

  public static function getParentGenreByGeneration($argGeneration, $argOriGenre = null)
  {
    $parentGenre = null;
    $generation = $argGeneration;
    $oriGenre = $argOriGenre ?? self::getCurrentGenre();
    if (is_numeric($generation) && is_numeric(strpos($oriGenre, '/')))
    {
      $oriGenreAry = explode('/', $oriGenre);
      if ($generation < count($oriGenreAry))
      {
        $tempGenreAry = array_reverse(array_slice(array_reverse($oriGenreAry), $generation));
        $parentGenre = implode('/', $tempGenreAry);
      }
    }
    return $parentGenre;
  }

  public static function getScriptPathByPathInfo($argPathInfo)
  {
    $scriptPath = null;
    $pathInfo = $argPathInfo;
    if (!Validation::isEmpty($pathInfo))
    {
      $folder = Util::getLRStr($pathInfo, '/', 'leftr') . '/';
      $file = Util::getLRStr($pathInfo, '/', 'right');
      if (Validation::isEmpty($file)) $file = 'index.php';
      else
      {
        if (!is_numeric(strpos($file, '.'))) $file .= '.php';
      }
      $scriptPath = $folder . $file;
    }
    return $scriptPath;
  }
}