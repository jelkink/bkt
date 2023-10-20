<?php

$session->Logout();

$gui->AddCenter("Logged out. Click <a href=\"index.php\">here</a> to return to front.");

header("Location: index.php");

?>
