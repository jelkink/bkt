<?php

$ref = do_query("SELECT tag, COUNT(tag) AS cnt FROM tags GROUP BY tag");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>tag</th><th>count</th><th></th></tr>\n");

while ($tag = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr>" .
    "<td style=\"text-align: left\"><a href=\"index.php?page=list_bookings&session=$session->id&tag=$tag->tag\">$tag->tag</a></td>" .
    "<td>$tag->cnt</td>" .
    "<td><a href=\"index.php?page=delete_tag&session=$session->id&tag=$tag->tag\"><img src=\"images/bin.png\" width=\"20px\"></a></td>" .
    "</tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
