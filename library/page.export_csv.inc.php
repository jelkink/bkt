<?php

// From: https://stackoverflow.com/questions/4249432/export-to-csv-via-php
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

// From: https://stackoverflow.com/questions/4249432/export-to-csv-via-php
function download_send_headers($filename)
{
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    $nextWeek = gmdate("D, d M Y H:i:s", time() + (7 * 24 * 60 * 60));
    header("Expires: {$nextWeek} GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

$accountNumber = HTTPContext::getInteger("accountNumber", 0);

$accounts = array();

$ref = do_query("SELECT * FROM accounts");
while ($account = mysqli_fetch_object($ref)) {
  $accounts["$account->id"] = $account->name;
}

if ($accountNumber > 0)
  $ref = do_query("SELECT * FROM bookings WHERE account = $accountNumber ORDER BY date, transaction, id");
else
  $ref = do_query("SELECT * FROM bookings ORDER BY date, transaction, id");

$exportArray = array();
$runningBalance = 0;

$prevTransaction = 0;
while ($booking = mysqli_fetch_object($ref)) {

    if ($accountNumber > 0)
        $runningBalance += $booking->amount;

    $arrayRow = array(

        "transactionID" => $booking->transaction,
        "date" => $booking->date,
        "bookingMonth" => substr($booking->date, 0, 4) . substr($booking->date, 5, 2),
        "account" => $booking->account,
        "accountName" => array_key_exists($booking->account, $accounts) ? $accounts["$booking->account"] : "",
        "currency" => $booking->currency,
        "amount" => $booking->amount / 100,
        "description" => $booking->description,
        "balance" => $runningBalance / 100
    );

    $exportArray[] = $arrayRow;
}

download_send_headers(date("Ymd") . "_export" . ($accountNumber > 0 ? "_$accountNumber" : "") . ".csv");
echo array2csv($exportArray);
die();

?>
