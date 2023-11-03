<?php

$template = HTTPContext::getString("template");

$gui->AddHeader("<script type=\"text/javascript\" src=\"http://momentjs.com/downloads/moment.min.js\"></script>");
$gui->AddHeader("<script type=\"text/javascript\" src=\"scripts/entry.js\"></script>");

$accounts = array();
$ref = do_query("SELECT id, name FROM accounts ORDER BY id");
while ($account = mysqli_fetch_object($ref)) {
  $accounts[] = ["id" => $account->id, "name" => $account->name];
}

$gui->AddCenter("<script>var accountsArray = " . json_encode($accounts) . "</script>");

$currencies = array();
$ref = do_query("SELECT abbreviation FROM currencies ORDER BY abbreviation");
while ($currency = mysqli_fetch_object($ref)) {
  $currencies[] = $currency->abbreviation;
}

if ($template != "") {

  $ref = do_query("SELECT code FROM templates WHERE name=\"$template\" LIMIT 1");

  if ($template_code = mysqli_fetch_object($ref))
    $template = "date " . date("Ymd") . "\n" . $template_code->code;
  else {
    $template = "";
  }
}

$gui->AddCenter("<script>var currenciesArray = " . json_encode($currencies) . "</script>");

$gui->AddCenter("<form method=\"post\" action=\"index.php?page=store_booking&session=$session->id\" id=\"newBookingForm\">\n");
$gui->AddCenter("<input type=\"hidden\" value=\"false\" name=\"reset\">\n");

$gui->AddCenter("<pre>date " . date("Ymd") . "</pre></br>");
$gui->AddCenter("<textarea name=\"bookingCode\" style=\"width:100%;\" rows=\"10\">$template</textarea>\n");

$gui->AddCenter("<center><table class=\"formTable\" id=\"entryTable\">\n");
$gui->AddCenter("<tr style=\"height=20px\"><td>&nbsp;</td></tr>\n");
$gui->AddCenter("</table></center>\n");

$gui->AddCenter("<input type=\"submit\" value=\"Submit\">\n");
$gui->AddCenter("</form>\n");
$gui->AddCenter("<button id=\"addLineBtn\" onclick=\"addLine()\">Add line</button>\n");

$ref = do_query("SELECT accounts.id AS id, account_types.name AS type, accounts.name AS name
    FROM accounts, account_types
    WHERE accounts.type = account_types.id
    ORDER BY accounts.id");

$gui->AddRight("<table>\n");

while ($account = mysqli_fetch_object($ref)) {

    $gui->AddRight("<tr style=\"font-size: 50%;\"><td>$account->id</td><td>$account->type</td><td>$account->name</td></tr>\n");
}

$gui->AddRight("</table>\n");


?>
