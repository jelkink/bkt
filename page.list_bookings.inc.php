<?php

$accountNumber = HTTPContext::getInteger("account", 0);
$transactionNumber = HTTPContext::getInteger("transaction", 0);

$accounts = array();

$ref = do_query("SELECT * FROM accounts");
while ($account = mysqli_fetch_object($ref)) {
  $accounts["$account->id"] = $account->name;
}

if ($accountNumber > 0)
  $ref = do_query("SELECT * FROM bookings WHERE account = $accountNumber ORDER BY transaction DESC, id");
elseif ($transactionNumber > 0)
  $ref = do_query("SELECT * FROM bookings WHERE transaction = $transactionNumber ORDER BY id");
else
  $ref = do_query("SELECT * FROM bookings ORDER BY transaction DESC, id");

if ($accountNumber > 0)
    $gui->AddCenter("<center><a href=\"index.php?page=export_csv&accountNumber=$accountNumber&session=$session->id\" target=\"_blank\">Export to CSV</a></center>\n");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th></th><th>date</th><th>account</th><th></th><th>currency</th><th>amount</th><th></th><th>description</th></tr>\n");

$prevTransaction = 0;
while ($booking = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr><td><a href=\"index.php?page=list_bookings&transaction=$booking->transaction&session=$session->id\">$booking->transaction</a></td>" .
    "<td>$booking->date</td>" .
    "<td style=\"text-align: right\"><a href=\"index.php?page=list_bookings&account=$booking->account&session=$session->id\">$booking->account</a></td>" .
    "<td>" . $accounts["$booking->account"] . "</td>" .
    "<td style=\"text-align: right\">$booking->currency</td>" .
    "<td style=\"text-align: right\">" . sprintf("%.2f", abs($booking->amount) / 100) . "</td>" .
    "<td>" . ($booking->amount < 0 ? "cr" : "") . "</td>" .
    "<td style=\"text-align: left\">$booking->description</td>");

  if ($booking->transaction != $prevTransaction) {
    $prevTransaction = $booking->transaction;
    $gui->AddCenter("<td><a href=\"index.php?page=delete_transaction&session=$session->id&transaction=$booking->transaction\"><img src=\"images/bin.png\" width=\"20px\"></a></td>");
  } else {
    $gui->AddCenter("<td></td>");
  }

  $gui->AddCenter("</tr>\n");
}

$gui->AddCenter("</table></center>\n");

if ($transactionNumber > 0) {

  $ref = do_query("SELECT * FROM notes WHERE booking = $transactionNumber LIMIT 1");

  if ($note = mysqli_fetch_object($ref))
    $gui->AddRight("<pre>$note->note</pre>\n");
}

?>
