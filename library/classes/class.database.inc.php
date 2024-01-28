<?php

class Database
{
  public static $handle;
  var $query;
  var $result;
  var $nrofrows;
  var $isresult;

  private $transactions = 0;
  public static $wasErrorInTransaction; // true if any query in transaction has failed

  function open()
  {
    Database::$handle = new mysqli("", "", "", "");

    do_query("SET NAMES utf8");
    mysqli_set_charset(Database::$handle, 'latin1');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    return Database::$handle;
  }

  function close()
  {
    Database::$handle->close();
  }
}

function do_query($query, $return_id = false)
{
  $res = mysqli_query(Database::$handle, "/* " . (isset($GLOBALS['page']) ? $GLOBALS['page'] : "") . " */ " . $query);

  if ($res) {
    if ($return_id) {
      $res = mysqli_insert_id(Database::$handle);
    }
  }

  return $res;
}

function do_scalar($query)
{
  $resource = do_query($query);
  if ($resource != null) {
    list ($result) = mysqli_fetch_row($resource);
    return $result;
  }
  return null;
}
