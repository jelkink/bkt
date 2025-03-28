<?php

$bookingCode = HTTPContext::getString("bookingCode", "");
$reset = HTTPContext::getString("reset", "false");

$date = HTTPContext::getArray("date");
$account = HTTPContext::getArray("account");
$currency = HTTPContext::getArray("currency");
$amount = HTTPContext::getArray("amount");
$credit = HTTPContext::getArray("credit");
$description = HTTPContext::getArray("description");

if ($reset == "true") {

  do_query("DELETE FROM bookings");
  do_query("DELETE FROM accounts");
  do_query("DELETE FROM templates");
  do_query("DELETE FROM currencies");
  do_query("DELETE FROM notes");
  do_query("DELETE FROM logs");
  // do_query("DELETE FROM tags"); // This should be implemented, but not yet working on export/import!
}

function process($code, &$gui, $currentDate = "") {

  $lines = explode("\n", str_replace(array("\r\n", "\n\r", "\r"), "\n", stripcslashes($code)));

  $gui->AddCenter("<pre>" . implode("\n", $lines) . "</pre>\n");

  $inTransaction = FALSE;
  $inTemplate = FALSE;

  $fullCommand = "";

  $currentCurrency = "EUR";

  $transactionID = do_scalar("SELECT MAX(transaction) FROM bookings");

  foreach ($lines as $line) {

    if (substr($line, 1, 1) != "#") {

      $fields = str_getcsv($line, " ");

      if (substr(urlencode($line), 0, 3) == "%09") {

        if ($inTransaction) $command = "continue_transaction";
        elseif ($inTemplate) $command = "continue_template";

        $fullCommand .= "\n" . $line;
      } else {

        $inTransaction = FALSE;

        if ($inTemplate) {
          $templateCode = str_replace("'", "`", $templateCode);
          do_query("INSERT INTO templates (name, code) VALUES (\"$templateName\", '$templateCode')");
          $inTemplate = FALSE;
        }

        $fullCommand = $line;
        $command = $fields[0];
      }

      switch($command) {

        case "create_account" :
          do_query("INSERT INTO accounts (id, type, name) VALUES ($fields[1], \"$fields[2]\", \"$fields[3]\")");
          break;

        case "date" :
          $currentDate = $fields[1];
          break;

        case "create_currency" :
          do_query("INSERT INTO currencies (abbreviation, rate, name) VALUES (\"$fields[1]\", $fields[2], \"$fields[3]\")");
          break;

        case "create_template" :
          $templateName = $fields[1];
          $templateCode = "";
          $inTemplate = TRUE;
          break;

        case "continue_template" :
          $templateCode .= substr($line, 1) . "\n";
          break;

        case "template" :
        case "t":
          $templateName = $fields[1];
          $ref = do_query("SELECT code FROM templates WHERE name = '$templateName'");
          if ($template = mysqli_fetch_object($ref)) {

            $templateCode = $template->code;
            for ($i = 2; $i < count($fields); $i++) {

              $templateCode = str_replace("$" . ($i - 1), $fields[$i], $templateCode);
            }

            $transactionID = process($templateCode, $gui, $currentDate);
          }
          break;

        case "currency" :
        case "c" :
          $currentCurrency = $fields[1];
          break;

        case "log" :
        case "diary" :
          $log = mysqli_real_escape_string(Database::$handle, implode(" ", array_slice($fields, 1)));
          do_query("INSERT INTO logs (date, entry) VALUES (\"$currentDate\", \"$log\")");
          break;

        case "transaction" :
        case "tr" :
          $transactionID++;
          $inTransaction = TRUE;
          $description = $fields[1];
          $currency = $currentCurrency;
          break;

        case "continue_transaction" :
          if (preg_match("/.*note/", $fields[0]) == 1) {

            $note = $fields[1];

            do_query("INSERT INTO notes (booking, note) VALUES ($transactionID, \"$note\")");
          } elseif (preg_match("/.*tag/", $fields[0]) == 1) {

            $tag = $fields[1];

            do_query("INSERT INTO tags (booking, tag) VALUES ($transactionID, \"$tag\")");
          } else {
            $account = $fields[0];

            $credit = FALSE;
            foreach (array_slice($fields, 1) as $field) {

              if (preg_match("/[A-Z]{3}/", $field) == 1) $currency = $field;
              if ($field == "cr") $credit = TRUE;
              if (preg_match("/[0-9\.]+/", $field) == 1) $amount = $field;
            }

            if (isset($amount) and is_numeric($amount) and is_numeric($account)) {

              $formattedAmount = ($credit ? -1 : 1) * floor($amount * 100);

              Database::$insert_booking_stmt->bind_param("isisds",
                $transactionID,
                $currentDate,
                $account, 
                $currency, 
                $formattedAmount, 
                $description
              );
              Database::$insert_booking_stmt->execute();
            } else {
              $gui->AddRight("ERROR: <pre>" . $fullCommand . "</pre> on date " . $currentDate . " (ID " . $transactionID . ")</br>");
            }
          }
          break;
      }
    }
  }

  if ($inTemplate) {
    $templateCode = str_replace("'", "`", $templateCode);
    do_query("INSERT INTO templates (name, code) VALUES (\"$templateName\", '$templateCode')");
    $inTemplate = FALSE;
  }

  return $transactionID;
}

if (strlen($bookingCode) > 0) process("date " . date("Ymd") . "\n" . $bookingCode, $gui);

$command = "";
for ($i = 0; $i < count($amount); $i++) {

  if ($amount[$i] != 0) {

    $command .= ($i == 0 ? "date " . $date[$i] . "\n" : "");
    $command .= ($i == 0 ? "transaction \"" . $description[$i] . "\"\n\t" : "\n\t");
    $command .= $account[$i] . (in_array($i, $credit) ? " cr " : " ") . $currency[$i] . " " . $amount[$i];
  }
}

process($command, $gui);

?>
