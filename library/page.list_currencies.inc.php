mysqli<?php

$ref = do_query("SELECT * FROM currencies ORDER BY abbreviation");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>name</th><th>abbreviation</th><th>exchange rate</th></tr>\n");

while ($currency = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr><td>$currency->name</td><td><a href=\"index.php?page=balance&currency=$currency->abbreviation&session=$session->id\">$currency->abbreviation</a></td><td style=\"text-align: right\">" .
    sprintf("%.5f", $currency->rate) . "</td></tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
