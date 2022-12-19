<?php

$id = HTTPContext::getInteger("note");

do_query("DELETE FROM notes WHERE id = $id");

header("Location: index.php?page=list_notes&session=$session->id");

?>
