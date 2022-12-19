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
    mysqli_set_charset( Database::$handle, 'latin1');

    return Database::$handle;
  }

  function close()
  {
    // We leave closing to the automatic PHP handler
    //    mysqli_close(Database::$handle);
    //   mail("programming@gmail.com","SQL Test Env Log " . $GLOBALS['page'],$GLOBALS['sql_debug_msg'],"From: Programming Department <programming@cantr.net>");
    Database::$handle->close();
  }

  function fetcharray()
  {
    if ($this->isresult) {
      $row = mysqli_fetch_array($this->result);
    } else {
      $row = 0;
    }
    return $row;
  }

  function execute()
  {
    $this->result = do_query($this->query) or die ("Invalid query: $this->query [" . mysqli_error(Database::$handle) . "]");

    return;
  }

  function fetchrow()
  {
    $row = mysqli_fetch_object($this->result);

    return $row;
  }

  function getid($tabel)
  {
    $this->query = "LOCK TABLES ids WRITE";
    $this->execute();

    $this->query = "SELECT id FROM ids WHERE tabel='$tabel'";
    $this->execute();

    $id = $this->fetchrow();
    $id->id++;

    $this->query = "UPDATE ids SET id=$id->id WHERE tabel='$tabel'";
    $this->execute();

    $this->query = "UNLOCK TABLES";
    $this->execute();

    return $id->id;
  }

  public function begin()
  {
    $this->transactions++;
    database::$wasErrorInTransaction = false;
    do_query("START TRANSACTION");
    return true;
  }

  public function commit()
  {
    if ($this->transactions > 0) {
      if (database::$wasErrorInTransaction) {
        $this->rollback();
        Logger::getLogger("database.inc.php")->info("ROLLBACK instead of COMMIT (" . $GLOBALS['page'] . ")");
        return false;
      }
      $this->transactions--;
      do_query("COMMIT");
      return true;
    }
    return false;
  }

  public function rollback()
  {
    if ($this->transactions > 0) {
      $this->transactions--;
      do_query("ROLLBACK");
      return true;
    }
    return false;
  }

  public function isOngoingTransaction()
  {
    return $this->transactions > 0;
  }
}

// does a query
function do_query($query, $return_id = false)
{
  //  $GLOBALS['sql_debug_msg'] .= $query . "\n";

  // start timer
  list ($usec, $sec) = explode(" ", microtime());
  $startTime = $usec + $sec % 10000000;
  // do query
  $res = mysqli_query(Database::$handle, "/* " . (isset($GLOBALS['page']) ? $GLOBALS['page'] : "") . " */ " . $query);

  // stop timer
  list ($usec, $sec) = explode(" ", microtime());
  $time = $usec + $sec % 10000000 - $startTime;

  // register query error
  if (!$res) {
    $mysqlerror = mysqli_error(Database::$handle);
    $char = $GLOBALS['character'];
    $date = date("D M d H:i:s Y");
    $stack = debug_backtrace();
    $stackmess = '';
    if (isset($stack[0])) {
      $stackmess .= $stack[0]['file'] . ', line ' . $stack[0]['line'];
      if (isset($stack[1])) {
        $stackmess .= " in " . $stack[1]['file'] . ', line ' . $stack[1]['line'];
      }
    }
    // error_log("[$date] [$stackmess] DO_QUERY error : '$query', character: $char, exception: '$mysqlerror'. \r\n", 3, '/home/http/' . _ENV . '.cantr.net/log/debug.clog');
    database::$wasErrorInTransaction = true;
  } else {
    if ($return_id) {
      $res = mysqli_insert_id(Database::$handle);
    }
  }

  // increase total SQL queries time
  // (it's a global variable)
  $GLOBALS ['sqltime'] += $time;
  // increase queries count - could be useful
  $GLOBALS ['sqlcount']++;
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

function fetch_all($ref)
{
  $elements = array();
  while ($element = mysqli_fetch_object($ref)) {
    $elements[] = $element;
  }
  return $elements;
}

function fetch_scalars($ref)
{
  $elements = array();
  while (list($element) = mysqli_fetch_row($ref)) {
    $elements[] = $element;
  }
  return $elements;
}
