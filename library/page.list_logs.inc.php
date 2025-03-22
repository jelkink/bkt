<?php

$ref = do_query("SELECT * FROM logs ORDER BY date DESC");

$gui->AddCenter("<center><table class=\"standardTable\">\n");
$gui->AddCenter("<tr><th>date</th><th>log</th><th></th></tr>\n");

while ($log = mysqli_fetch_object($ref)) {

  $gui->AddCenter("<tr><td style=\"text-align: right\">" .
    "$log->date</td>" .
    "<td style=\"text-align: left\"><pre>$log->entry</pre></td>" .
    "<td><a href=\"index.php?page=delete_log&session=$session->id&log=$log->id\"><img src=\"images/bin.png\" width=\"20px\"></a></td>" .
    "</tr>\n");
}

$gui->AddCenter("</table></center>\n");

?>
