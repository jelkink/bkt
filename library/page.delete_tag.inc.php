<?php

$tag = HTTPContext::getString("tag");

do_query("DELETE FROM tags WHERE tag = '$tag'");

header("Location: index.php?page=list_tags&session=$session->id");

?>
