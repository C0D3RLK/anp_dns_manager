<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/nov/2023
*/
if (class_exists('GENERAL_CONFIGURATION')!=true) {
  require_once './config/general.php';
}

if (class_exists('DB_COMM')!=true) {
  require_once './config/db.php';
}

$DNS_MAN = new DB_COMM();
$DNS_MAN::GENERAL_VARIABLES();

if ($DNS_MAN->gen_check_db_data(false,"SELECT * FROM users") == true){

  #initiate installation
  #Create user table

  $DNS_MAN->redirect_route('login');

}else{
  $DNS_MAN->initialise();

    $GET_SYS_KEY = json_decode($DNS_MAN->external_com($GLOBALS['SYS-KEY-SERVER_URL']),true);

    if ($GET_SYS_KEY['status'] != true) {
      echo $GLOBALS['ERROR_MSG']['SYS_KEY_ERROR'];
      exit;
    }

  unset($_POST['pwd']);
  $FILTER_PASS = base64_encode(md5($_POST['password']));
  unset($_POST['password']);

  $USER_TAG = md5($_POST['email']);
  $COLUMN_USER = "name,password,email,user_tag,privileges,sys_key,sys_key_status";
  $DATA_USER = "'".$_POST['name']."','".$FILTER_PASS."','".$_POST['email']."','".$USER_TAG."','0','".$GET_SYS_KEY['key']."','1'";

  $COLUMN_DOMAIN = "user_tag,cloudfapi,cloudfdomain,cloudfemail";
  $DATA_DOMAIN = "'".$USER_TAG."','".$_POST['cloudfapi']."','".$_POST['cloudfdomain']."','".$_POST['cloudfemail']."'";

  if($DNS_MAN->create_entry('users',$COLUMN_USER,$DATA_USER) && $DNS_MAN->create_entry('domains',$COLUMN_DOMAIN,$DATA_DOMAIN)){
    $DNS_MAN->redirect_route('login?status=202-Reg-Install');
  }

}
