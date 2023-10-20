<?php

$currency = HTTPContext::getString("currency", "*");
$month = HTTPContext::getInteger("month", 0);

$gui->AddHeader("<script type=\"text/javascript\" src=\"scripts/balance.js\"></script>");

$ref = do_query("SELECT abbreviation FROM currencies ORDER BY abbreviation");
$gui->AddCenter("<center><a href=\"index.php?page=balance&currency=*&session=$session->id\">*</a>");
while ($curr = mysqli_fetch_object($ref)) {

    $gui->AddCenter(" | <a href=\"index.php?page=balance&currency=$curr->abbreviation&session=$session->id\">$curr->abbreviation</a>");
}
$gui->AddCenter("</center>\n");


## DISPLAY BALANCE SHEET

$assets = array();
$liabilities = array();

if ($currency != "*") {
    $ref = do_query("SELECT account, SUM(amount) AS balance, name, type FROM bookings, accounts WHERE accounts.id = bookings.account AND bookings.currency = '$currency' AND (type = 'A' OR type = 'L') GROUP BY account ORDER BY type, account");
} else {
    $ref = do_query("SELECT account, SUM(amount * rate) AS balance, accounts.name, type FROM bookings, accounts, currencies WHERE accounts.id = bookings.account AND bookings.currency = currencies.abbreviation AND (type = 'A' OR type = 'L') GROUP BY account ORDER BY type, account");
}

while ($balance = mysqli_fetch_object($ref)) {

  if (abs($balance->balance) > .01) {
      switch ($balance->type) {

          case "A" : $assets[] = $balance; break;
          case "L" : $liabilities[] = $balance; break;
      }
  }
}

$gui->AddCenter("<h2>Balance sheet ($currency)</h2>\n");

$gui->AddCenter("<center><table>\n");
$gui->AddCenter("<tr><td colspan=\"3\" style=\"text-align: center\">ASSETS</td>" .
  "<td colspan=\"3\" style=\"text-align: center\">LIABILITIES</td></tr>\n");
$gui->AddCenter("<tr><th>account</th><th></th><th>balance</th><th></th><th>account</th><th></th><th>balance</th></tr>\n");

for ($i = 0; $i < max(count($assets), count($liabilities)); $i++) {

  if (count($assets) > $i)
    $gui->AddCenter("<tr><td style=\"text-align: right\"><a href=\"index.php?page=list_bookings&account=" . $assets[$i]->account . "&session=$session->id\">" . $assets[$i]->account . "</a></td><td>" . $assets[$i]->name . "</td><td style=\"text-align: right\">" . sprintf("%.2f", abs($assets[$i]->balance) / 100) . "</td><td>" . ($assets[$i]->balance > -0.004 ? "" : "cr") . "</td>\n");
  else
    $gui->AddCenter("<tr><td></td><td></td><td></td><td></td>\n");

  if (count($liabilities) > $i)
    $gui->AddCenter("<td style=\"text-align: right\"><a href=\"index.php?page=list_bookings&account=" . $liabilities[$i]->account . "&session=$session->id\">" . $liabilities[$i]->account . "</a></td><td>" . $liabilities[$i]->name . "</td><td style=\"text-align: right\">" . sprintf("%.2f", abs($liabilities[$i]->balance) / 100) . "</td><td>" . ($liabilities[$i]->balance > 0.004 ? "cr" : "") . "</td></tr>\n");
  else
    $gui->AddCenter("<td></td><td></td><td></td></tr><td></td>\n");
}

$gui->AddCenter("</table></center>\n");

## DISPLAY EXPENSES SHEET

$expenses = array();
$income = array();



if ($currency != "*") {
    $ref = do_query(sprintf("SELECT account, SUM(amount) AS balance, name, type FROM bookings, accounts WHERE accounts.id = bookings.account AND bookings.currency = \"$currency\" AND (type = \"E\" OR type = \"I\") AND MONTH(date) = MONTH(CURRENT_DATE %s INTERVAL %d MONTH) AND YEAR(date) = YEAR(CURRENT_DATE %s INTERVAL %d MONTH) GROUP BY account ORDER BY balance DESC, type, account", ($month >= 0 ? "+" : "-"), abs($month), ($month >= 0 ? "+" : "-"), abs($month)));
} else {
    $ref = do_query(sprintf("SELECT account, SUM(amount * rate) AS balance, accounts.name, type FROM bookings, accounts, currencies WHERE accounts.id = bookings.account AND bookings.currency = currencies.abbreviation AND (type = \"E\" OR type = \"I\") AND MONTH(date) = MONTH(CURRENT_DATE %s INTERVAL %d MONTH) AND YEAR(date) = YEAR(CURRENT_DATE %s INTERVAL %d MONTH) GROUP BY account ORDER BY balance DESC, type, account", ($month >= 0 ? "+" : "-"), abs($month), ($month >= 0 ? "+" : "-"), abs($month)));
}

while ($balance = mysqli_fetch_object($ref)) {

  switch ($balance->type) {

    case "A" : $assets[] = $balance; break;
    case "L" : $liabilities[] = $balance; break;
    case "E" : $expenses[] = $balance; break;
    case "I" : $income[] = $balance; break;
  }
}

$gui->AddCenter("<h2>Expense account ($currency)</h2>\n");

$gui->AddCenter("<form method=\"post\" action=\"index.php?page=balance&session=$session->id&currency=$currency\" id=\"monthForm\">\n");
$gui->AddCenter("<center><select name=\"month\" onchange=\"changeMonth()\">\n");
for ($i = -5; $i < +7; $i++) {
  $date_str = date('F', strtotime(sprintf("%+d month", $i)));
  $selected = ($i == $month ? " selected" : "");
  $gui->AddCenter(sprintf("<option value=\"%d\"%s>%s</option>\n", $i, $selected, $date_str));
}
$gui->AddCenter("</select></center></form><br/>\n");

$gui->AddCenter("<center><table>\n");
$gui->AddCenter("<tr><td colspan=\"3\" style=\"text-align: center\">EXPENSES</td>" .
  "<td colspan=\"3\" style=\"text-align: center\">INCOME</td></tr>\n");
$gui->AddCenter("<tr><th>account</th><th></th><th>balance</th><th></th><th>account</th><th></th><th>balance</th></tr>\n");


for ($i = 0; $i < max(count($expenses), count($income)); $i++) {

  if (count($expenses) > $i)
    $gui->AddCenter("<tr><td style=\"text-align: right\"><a href=\"index.php?page=list_bookings&account=" . $expenses[$i]->account . "&session=$session->id\">" . $expenses[$i]->account . "</a></td><td>" . $expenses[$i]->name . "</td><td style=\"text-align: right\">" .  sprintf("%.2f", abs($expenses[$i]->balance) / 100) . "</td><td>" . ($expenses[$i]->balance > -0.004 ? "" : "cr")  . "</td>\n");
  else
    $gui->AddCenter("<tr><td></td><td></td><td></td><td></td>\n");

  if (count($income) > $i)
    $gui->AddCenter("<td style=\"text-align: right\"><a href=\"index.php?page=list_bookings&account=" . $income[$i]->account . "&session=$session->id\">" . $income[$i]->account . "</a></td><td>" . $income[$i]->name . "</td><td style=\"text-align: right\">" .  sprintf("%.2f", abs($income[$i]->balance) / 100) . "</td><td>" . ($income[$i]->balance > 0.004 ? "cr" : "") . "</td></tr>\n");
  else
    $gui->AddCenter("<td></td><td></td><td></td></tr><td></td>\n");
}

$gui->AddCenter("</table></center>\n");

?>
