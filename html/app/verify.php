<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/ov/2023
*/
if (class_exists('GENERAL_CONFIGURATION')!=true) {
  require_once './config/general.php';
}

if (class_exists('DB_COMM')!=true) {
  require_once './config/db.php';
}

$DNS_MAN = new DB_COMM();
$DNS_MAN::GENERAL_VARIABLES();
if ($DNS_MAN->gen_check_db_data(false,"SELECT * FROM users") != true){

  $DNS_MAN->redirect_route('install');

}else{
  $DNS_MAN->redirect_route('login');
}
