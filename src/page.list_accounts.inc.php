<?php

$ref = do_query("SELECT accounts.id AS id, account_types.name AS type, accounts.name AS name
    FROM accounts, account_types
    WHERE accounts.type = account_types.id
    ORDER BY accounts.id");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>id</th><th>type</th><th>name</th></tr>\n");

while ($account = mysqli_fetch_object($ref)) {

    $gui->AddCenter("<tr><td><a href=\"index.php?page=list_bookings&account=$account->id&session=$session->id\">$account->id</a></td><td>$account->type</td><td style=\"text-align: left\">$account->name</td></tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
