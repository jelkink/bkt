<?php

$id = HTTPContext::getInteger("log");

do_query("DELETE FROM logs WHERE id = $id");

header("Location: index.php?page=list_logs&session=$session->id");

?>
