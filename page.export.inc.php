<?php

$gui->AddHeader("<script type=\"text/javascript\" src=\"scripts/allow_tab.js\"></script>");

$gui->AddCenter("<form method=\"post\" action=\"index.php?page=store_booking&session=$session->id\">\n");
$gui->AddCenter("<center><input type=\"submit\" value=\"Reload\"></center>\n");
$gui->AddCenter("<input type=\"hidden\" value=\"true\" name=\"reset\">\n");
$gui->AddCenter("<textarea class=\"codeExport\" style=\"width: 100%\" rows=\"40\" name=\"bookingCode\">\n");

$gui->AddCenter("\n# Create accounts\n");

$ref = do_query("SELECT * FROM accounts ORDER BY id");
while ($account = mysqli_fetch_object($ref)) {

  $gui->AddCenter(sprintf("create_account %03d %1s \"%s\"", $account->id, $account->type, $account->name));
}

$gui->AddCenter("\n# Create currencies\n");

$ref = do_query("SELECT * FROM currencies ORDER BY abbreviation");
while ($currency = mysqli_fetch_object($ref)) {

  $gui->AddCenter(sprintf("create_currency %3s %f \"%s\"", $currency->abbreviation, $currency->rate, $currency->name));
}

$gui->AddCenter("\n# Prepare templates");

$ref = do_query("SELECT * FROM templates ORDER BY name");
while ($template = mysqli_fetch_object($ref)) {

  $gui->AddCenter(sprintf("\ncreate_template %s\n\t%s", $template->name, substr(implode("\n\t", explode("\n", $template->code)), 0, -2)));
}

$gui->AddCenter("\n# Add transaction data\n");

$prevTransaction = 0;
$currDate = "";
$ref = do_query("SELECT account, transaction, date, currency, description, amount, note FROM bookings LEFT JOIN notes ON (notes.booking = bookings.transaction) ORDER BY date, transaction, bookings.id");
while ($booking = mysqli_fetch_object($ref)) {

  if ($booking->transaction != $prevTransaction) {

    if (str_replace("-", "", $booking->date) != $currDate) {

      $currDate = str_replace("-", "", $booking->date);
      $gui->AddCenter(sprintf("\ndate %s", $currDate));
    }

    $gui->AddCenter(sprintf("transaction \"%s\"", $booking->description));
    $prevTransaction = $booking->transaction;

    if ($booking->note)
      $gui->AddCenter("\tnote \"$booking->note\"");
  }

  $gui->AddCenter(sprintf("\t%03d %s%3s %.2f", $booking->account,
    ($booking->amount < 0 ? "cr " : ""), $booking->currency, abs($booking->amount) / 100));
}

$gui->AddCenter("\ncurrency EUR\n");

$gui->AddCenter("</textarea></form>\n");

?>
