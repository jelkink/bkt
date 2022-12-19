<?php

class HTTPContext
{

  private function HTTPContext()
  {
  }

  private static function _keyExist($keyword)
  {
    return isset($_REQUEST[$keyword]);
  }

  public static function getString($keyword, $defaultValue = null, $escapeString = true)
  {
    if (self::_keyExist($keyword)) {

      $data = $_REQUEST[$keyword];

      if (!is_string($data)) exit("Expected string at $keyword\n");

      if ($escapeString)
        return mysqli_real_escape_string(Database::$handle, $data);
      else
        return $data;
    }
    return $defaultValue;
  }

  public static function getInteger($keyword, $defaultValue = 0)
  {
    if (self::_keyExist($keyword)) {
      return intval($_REQUEST[$keyword]);
    }
    return $defaultValue;
  }

  public static function getArray($keyword, $defaultValue = array())
  {
    if (self::_keyExist($keyword)) {
      return $_REQUEST[$keyword];
    }
    return $defaultValue;
  }

  public static function getBoolean($keyword, $defaultValue = false)
  {
    if (self::_keyExist($keyword)) {
      $var = $_REQUEST[$keyword];
      if ($var == "true") {
        return true;
      } elseif ($var == "false") {
        return false;
      }

      return !!$var;
    }
    return $defaultValue;
  }
}
