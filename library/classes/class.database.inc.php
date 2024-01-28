<?php

class Database
{
  public static $handle;
  public static $insert_booking_stmt;

  function open()
  {
    Database::$handle = new mysqli("", "", "", "");

    do_query("SET NAMES utf8");
    mysqli_set_charset(Database::$handle, 'latin1');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    Database::$insert_booking_stmt = Database::$handle->prepare(
      "INSERT INTO bookings (transaction, date, account, currency, amount, description) VALUES (?, STR_TO_DATE(?, '%Y%m%d'), ?, ?, ?, ?)"
    );

    return Database::$handle;
  }

  function close()
  {
    Database::$insert_booking_stmt->close();
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
