<?php

class Session {

  function Session()
  {
  }

  function LoginForm() {
    $s = "

<form method=\"POST\" action=\"index.php?page=login\" class=\"form1\">
  <table>
    <tr><td>Login:</td><td><input type=\"text\" size=20 class=\"input-field\" name=\"login\"></td></tr>
    <tr><td>Password:</td><td><input type=\"password\" size=20 class=\"input-field\" name=\"password\"></td></tr>
    <tr><td colspan=2><input type=\"submit\" text=\"Login\"></td></tr>
  </table>
</form>

";

    return $s;
  }

  function Create($login) {

    $this->id = uniqid();

    do_query("INSERT INTO sessions (id, login) VALUES (\"$this->id\", \"$login\")");

    return $this->id;
  }

  function VerifyLogin($id) {

    if (ctype_alnum($id)) {

      $res = mysqli_fetch_object(do_query("SELECT login FROM sessions WHERE id = \"$id\""));

      if ($res) {

        $this->login = $res->login;
        $this->id = $id;

        return true;
      }
    }

    return false;
  }

  function Logout() {

    do_query("DELETE FROM sessions WHERE login = \"$this->login\"");
  }
}

?>
