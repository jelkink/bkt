<?php

$ref = do_query("SELECT * FROM notes ORDER BY booking");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>booking</th><th>note</th><th></th></tr>\n");

while ($note = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr><td style=\"text-align: right\">" .
    "<a href=\"index.php?page=list_bookings&transaction=$note->booking&session=$session->id\">$note->booking</a></td>" .
    "<td style=\"text-align: left\"><pre>$note->note</pre></td>" .
    "<td><a href=\"index.php?page=delete_note&session=$session->id&note=$note->id\"><img src=\"images/bin.png\" width=\"20px\"></a></td>" .
    "</tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
