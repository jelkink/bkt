<?php

$transaction = HTTPContext::getInteger("transaction");

do_query("DELETE FROM bookings WHERE transaction = $transaction");
do_query("DELETE FROM notes WHERE booking = $transaction");

header("Location: index.php?page=list_bookings&session=$session->id");

?>
