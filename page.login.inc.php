<?php

$login = HTTPContext::getString("login", "");
$password = HTTPContext::getString("password", "");

if (ctype_alnum($login) && ctype_alnum($password)) {

  $res = mysqli_fetch_object(do_query("SELECT password FROM users WHERE login = \"$login\""));

  if (password_verify($password, $res->password)) {

    $session->Create($login);

    $gui->AddCenter("<p>Login successful. <a href=\"index.php?page=balance&session=$session->id\">Continue</a>.</p>");
  } else {

    $gui->AddCenter("<p>ERROR: Wrong password or username.</p>");
  }
} else {

  $gui->AddCenter("<p>ERROR: Only alphanumeric user name and password are accepted.</p>");
}

header("Location: index.php?page=balance&session=$session->id")

?>
