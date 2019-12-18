<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Cache;
use Jtbc\Cache;
use Jtbc\Path;
use Jtbc\Validation;
use Jtbc\Exception\FileException;
use Config\Cache\FileCache as Config;

class FileCache implements Cache
{
  private $filename;
  private $expire;

  private function getCacheDir()
  {
    return Path::getActualRoute(Config::DIR);
  }

  private function getFileData()
  {
    $fileData = null;
    $fileContent = file_get_contents($this -> filename);
    if (!Validation::isEmpty($fileContent))
    {
      $fileContentArray = json_decode($fileContent, true);
      if (is_array($fileContentArray))
      {
        if (array_key_exists('expire', $fileContentArray) && array_key_exists('data', $fileContentArray))
        {
          $currentExpire = $fileContentArray['expire'];
          $currentData = $fileContentArray['data'];
          if (is_null($currentExpire) || (is_numeric($currentExpire) && $currentExpire <= time())) $fileData = $currentData;
        }
      }
    }
    return $fileData;
  }

  private function putFileData($argData)
  {
    $data = $argData;
    $fileData = [];
    $fileData['expire'] = $this -> expire;
    $fileData['data'] = $data;
    $bool = file_put_contents($this -> filename, json_encode($fileData));
    return $bool;
  }

  public function exist($argName)
  {
    $bool = false;
    $name = $argName;
    $dir = $this -> getCacheDir();
    $cacheFilename = $dir . '/' . $name . '.cache';
    if (is_file($cacheFilename)) $bool = true;
    return $bool;
  }

  public function get($argName)
  {
    $result = null;
    $name = $argName;
    if ($this -> exist($name))
    {
      $dir = $this -> getCacheDir();
      $this -> filename = $dir . '/' . $name . '.cache';
      $result = $this -> getFileData();
    }
    return $result;
  }

  public function put($argName, $argData, $argExpire = null)
  {
    $bool = false;
    $name = $argName;
    $data = $argData;
    $expire = $argExpire;
    $cacheBool = false;
    $dir = $this -> getCacheDir();
    if (!is_dir($dir)) throw new ErrorException('Could not find the folder "' . $dir . '"', 50404);
    else
    {
      $this -> filename = $dir . '/' . $name . '.cache';
      $this -> expire = $expire;
      $bool = $this -> putFileData($data);
    }
    return $bool;
  }

  public function remove($argName = '')
  {
    $name = $argName;
    $cacheBool = false;
    $dir = $this -> getCacheDir();
    if (!Validation::isEmpty($name))
    {
      $cacheFilename = $dir . '/' . $name . '.cache';
      $cacheBool = unlink($cacheFilename);
    }
    else
    {
      $cacheBool = true;
      $cdirs = dir($dir);
      while($entry = $cdirs -> read())
      {
        $filename = $dir . '/' . $entry;
        if (is_file($filename))
        {
          if (!unlink($dir . '/' . $entry)) $cacheBool = false;
        }
      }
      $cdirs -> close();
    }
    return $cacheBool;
  }

  public function removeByKey($argKey, $argMode = 0)
  {
    $key = $argKey;
    $mode = $argMode;
    $cacheBool = false;
    $dir = $this -> getCacheDir();
    if (!Validation::isEmpty($key))
    {
      $cacheBool = true;
      $cdirs = dir($dir);
      while($entry = $cdirs -> read())
      {
        $strpos = strpos($entry, $key);
        if (($mode == 0 && $strpos == 0) || ($mode == 1 && $strpos >= 0))
        {
          $filename = $dir . '/' . $entry;
          if (is_file($filename))
          {
            if (!unlink($dir . '/' . $entry)) $cacheBool = false;
          }
        }
      }
      $cdirs -> close();
    }
  }
}