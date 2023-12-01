<?php
if (session_status() == PHP_SESSION_NONE ) {
  session_start();
}
unset($_SESSION['USER_LOGGED']);
session_destroy();
header("HTTP/1.1 302 Moved Temporarily");
header("location: /", true, 302);
exit;
 ?>
