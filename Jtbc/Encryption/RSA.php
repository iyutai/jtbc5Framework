<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace Jtbc\Encryption;

class RSA
{
  private static function getString($argStr)
  {
    $string = '';
    $str = $argStr;
    if (is_numeric($str))
    {
      $string = strval($str);
    }
    else if (is_string($str))
    {
      $string = $str;
    }
    return $string;
  }

  public static function publicEncrypt($argData, $argPublicKey)
  {
    $result = null;
    $data = self::getString($argData);
    $publicKey = self::getString($argPublicKey);
    if (is_file($publicKey))
    {
      $encryptData = '';
      $publicKeyContent = openssl_pkey_get_public(file_get_contents($publicKey));
      openssl_public_encrypt($data, $encryptData, $publicKeyContent);
      openssl_free_key($publicKeyContent);
      $result = base64_encode($encryptData);
    }
    return $result;
  }

  public static function privateDecrypt($argData, $argPrivateKey)
  {
    $result = null;
    $data = self::getString($argData);
    $privateKey = self::getString($argPrivateKey);
    if (is_file($privateKey))
    {
      $decryptData = '';
      $privateKeyContent = openssl_pkey_get_private(file_get_contents($privateKey));
      openssl_private_decrypt(base64_decode($data), $decryptData, $privateKeyContent);
      openssl_free_key($privateKeyContent);
      $result = $decryptData;
    }
    return $result;
  }

  public static function privateSign($argData, $argPrivateKey)
  {
    $result = null;
    $data = self::getString($argData);
    $privateKey = self::getString($argPrivateKey);
    if (is_file($privateKey))
    {
      $sign = '';
      $privateKeyContent = openssl_pkey_get_private(file_get_contents($privateKey));
      openssl_sign($data, $sign, $privateKeyContent);
      openssl_free_key($privateKeyContent);
      $result = base64_encode($sign);
    }
    return $result;
  }

  public static function publicVerify($argData, $argSign, $argPublicKey)
  {
    $result = null;
    $data = self::getString($argData);
    $sign = self::getString($argSign);
    $publicKey = self::getString($argPublicKey);
    if (is_file($publicKey))
    {
      $publicKeyContent = openssl_pkey_get_public(file_get_contents($publicKey));
      $result = openssl_verify($data, base64_decode($sign), $publicKeyContent);
      openssl_free_key($publicKeyContent);
    }
    return $result;
  }
}