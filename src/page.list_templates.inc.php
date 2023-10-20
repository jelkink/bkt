<?php

$ref = do_query("SELECT * FROM templates ORDER BY name");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>name</th><th>code</th>\n");

while ($template = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr><td style=\"text-align: left\">" .
    "<a href=\"index.php?page=new_booking&session=$session->id&template=$template->name\">$template->name</a></td>" .
    "<td style=\"text-align: left\"><pre>$template->code</pre></td></tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
