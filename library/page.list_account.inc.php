<?php

$ref = do_query("SELECT accounts.id AS id, account_types.name AS type, accounts.name AS name
    FROM accounts, account_types
    WHERE accounts.type = account_types.id
    ORDER BY accounts.id")

$gui->AddCenter("<center><table>\n");

while ($account = mysqli_fetch_object($ref)) {

    $gui->AddCenter("<tr><td>$account->id</td><td>$account->type</td><td>$account->name</td></tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
