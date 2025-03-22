<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ini_set('display_errors', '1');

$path_to_library = "../library/";

require_once($path_to_library . "classes/class.database.inc.php");
require_once($path_to_library . "classes/class.httpcontext.inc.php");
require_once($path_to_library . "classes/class.session.inc.php");
require_once($path_to_library . "classes/class.gui.inc.php");

$dbh = new Database;
$gui = new GUI;

$msg = HTTPContext::getString("message", null, false);
$gui->AddRight("<font color=\"red\">$msg</font>\n");

$gui->AddHeader("<script type=\"text/javascript\" src=\"https://code.jquery.com/jquery-3.2.1.min.js\"></script>");
// $gui->AddHeader("<script type=\"text/javascript\" src=\"scripts/main.js\"></script>");

if (!$dbh->open()) {

	$gui->AddCenter("<p>ERROR: Cannot access database.</p>");
} else {

	$page = HTTPContext::getString('page', '');
	$session = new Session($dbh);

	if ($page == '') {

		$gui->AddCenter($session->LoginForm());
	} else if ($page == "login") {

		include($path_to_library . "page.login.inc.php");
	} else {

		if ($session->VerifyLogin(HTTPContext::getString('session', ''))) {

			$gui->AddLeft("Logged in as $session->login<br/><br/>\n" .
			"<a href=\"index.php?page=new_booking&session=$session->id\">Add booking</a><br />\n" .
			"<a href=\"index.php?page=balance&session=$session->id\">Balance</a><br />\n" .
			"<a href=\"index.php?page=list_accounts&session=$session->id\">List accounts</a><br />\n" .
			"<a href=\"index.php?page=list_bookings&session=$session->id\">List bookings</a><br />\n" .
			"<a href=\"index.php?page=list_notes&session=$session->id\">List notes</a><br />\n" .
			"<a href=\"index.php?page=list_tags&session=$session->id\">List tags</a><br />\n" .
			"<a href=\"index.php?page=list_logs&session=$session->id\">List logs</a><br />\n" .
			"<a href=\"index.php?page=list_templates&session=$session->id\">List templates</a><br />\n" .
			"<a href=\"index.php?page=list_currencies&session=$session->id\">List currencies</a><br />\n" .
			"<a href=\"index.php?page=export&session=$session->id\">Export to BKT</a><br />\n" .
			"<a href=\"index.php?page=export_csv&session=$session->id\" target=\"_blank\">Export to CSV</a><br />\n" .
			"<br /><br /><a href=\"index.php?page=logout&session=$session->id\">Logout</a><br />\n");

			if (file_exists($path_to_library . "page." . $page . ".inc.php")) {

				include($path_to_library . "page." . $page . ".inc.php");
			} else {

				$msg = "ERROR: Page " . $page . " does not exist.";
				header("Location: index.php?session=$session->id&message=" . urlencode($msg));
			}
		} else {

			$msg = "ERROR: Session invalid.";
			header("Location: index.php?message=" . urlencode($msg));
		}
	}
}

$gui->Render();

?>
