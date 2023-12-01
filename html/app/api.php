<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/nov/2023
*/

if (session_status() == PHP_SESSION_NONE ) {
  session_start();
}

if (class_exists('GENERAL_CONFIGURATION')!=true) {
  require_once './config/general.php';
}

if (class_exists('DB_COMM')!=true) {
  require_once './config/db.php';
}

class API_ERRANDS extends DB_COMM{

  public function get_domain_history(){
    $OUTPUT = new stdClass;
    $OUTPUT->status['data'] = $this->gen_get_db_data("update_tracker","domain='$_POST[domain]' AND user_tag='$_POST[user_tag]' ORDER by id DESC LIMIT 3");
      if (COUNT(json_decode(json_encode($OUTPUT),true)['status']['data']) > 0) {
      $this::response_code("201");
      return $OUTPUT;
    }
    $this::response_code("201");
    $OUTPUT->status['domain'] = $_POST['domain'];
    $OUTPUT->status['reply'] = "No Update Data";
    return $OUTPUT;

  }



}

$DNS_MAN = new API_ERRANDS();
$DNS_MAN::GENERAL_VARIABLES();

if (!$DNS_MAN->is_user_loggedin()) {
  $DNS_MAN::response_code("403");
  exit;
}


if ($_POST['type'] == "json") {

if ($_POST['reply_header'] == 'true') {
  header('Content-Type: application/json; charset=utf-8');
}

if ($_POST['format'] == 'false') {
echo json_encode($DNS_MAN->get_domain_history());
exit;
}

if ($_POST['format'] == "pretty") {
  echo json_encode($DNS_MAN->get_domain_history(),JSON_PRETTY_PRINT);
  exit;
}

}


?>
