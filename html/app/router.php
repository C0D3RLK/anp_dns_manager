<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/nov/2023
*/

$LOCATION = strtolower(strtok($_SERVER['REQUEST_URI'],"?"));

switch ($LOCATION) {

  case '/':
  require_once './app/verify.php';
  require_once './app/footer.php';
  break;

  case '/default':
  require_once './app/verify.php';
  require_once './app/footer.php';
  break;

  case '/api':
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once './app/api.php';
  // require_once './app/footer.php';
  break;
}

  case '/404':
  require_once './404.php';
  require_once './app/footer.php';
  break;

  case '/install':
  require_once './app/installation.php';
  require_once './app/footer.php';
  break;

  case '/install-complete':
  require_once './app/complete_registration.php';
  require_once './app/footer.php';
  break;

  default:

  if (file_exists("./".$LOCATION.".php")) {
    require_once './'.$LOCATION.".php";
    require_once './app/footer.php';
    break;
  }

  require_once './404.php';
  require_once './app/footer.php';
  break;
}
