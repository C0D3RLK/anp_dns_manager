<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/Nov/2023
* General setting, variables and functions class file
*/
class GENERAL_CONFIGURATION{

  public $PAGE;

  public function redirect_route($FILE = "/"){
    header("HTTP/1.1 302 Moved Temporarily");
    header("location: /".$FILE, true, 302); //returning account
    return true;
    exit;
  }

  public function external_com($url = "https://public.kanthzone.com/api"){


    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    #This headers contains public keys do not change anything
    $headers = array(
      "Accept: application/json",
      "User-Agent: Kanthzone PublicAPI Client V1.0",
      "Authorization: Bearer 069fb7a93ca37f0f7e73612632d5fcbd"
    );
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLINFO_HEADER_OUT, $headers);

    //for debug only!
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $OUTPUT = curl_exec($curl);
    curl_close($curl);

    return $OUTPUT;

  }

  public function response_code($CODE = "200"){
    $RESPONSE_CODES = array(
      "200" => "OK",
      "201" =>  "Created",
      "201" =>  "Accepted",
      "400" =>  "Bad Request",
      "401" =>  "Unauthorized",
      "403" =>  "Forbidden",
      "404" =>  "Not Found"
    );

    if (in_array($CODE,array_keys($RESPONSE_CODES))) {
      header("HTTP/1.1 ".$CODE." ".$RESPONSE_CODES[$CODE]);
    }

  }

  public function get_location(){
    return strtok($_SERVER['REQUEST_URI'],"?");
  }

  public function multi_reader($DATA,$VAL){
    $characters[] = $DATA; // decode the feed
    foreach ($characters as $character) {
      $OUTPUT   = $character->$VAL;
    }
    return $OUTPUT;
  }

  public function set_time_zone(){
    date_default_timezone_set("Asia/Kuala_Lumpur");
  }

  public function php_configuration(){
    // if (isset($api)){$new_log_file = date("dmy").".log"; }else {$new_log_file = "errors.log"; }
    ini_set('error_reporting', E_ALL | E_STRICT);
    // ini_set('log_errors',TRUE);
    ini_set('html_errors',false);
    // ini_set('error_log', dirname(__FILE__).'/logs/'.$new_log_file);
    ini_set('display_errors', 0);
  }

  public function general_format(){
    $this->set_time_zone();
    $this->php_configuration();
    $DFORMAT     = "Y-m-d H:i:s";
    $HOUR_FORMAT = "Y-m-d H";
    $TOKEN_VALIDITY_FORMAT = "Y-m-d H";
    $OUTPUT = new stdClass;
    $OUTPUT->DATE_FORMAT = $DFORMAT;
    $OUTPUT->TODAY       = date($DFORMAT);
    $OUTPUT->NOW_HOUR    = date($HOUR_FORMAT);
    $OUTPUT->NOW          = date($DFORMAT);
    $OUTPUT->TOKEN_VALIDITY_FORMAT = date($TOKEN_VALIDITY_FORMAT);
    return $OUTPUT;
  }

  public function GENERAL_VARIABLES(){
    define('DEBUG_MODE',true);
    define("WUI_VERSION" ,"v1.0.0");
    $GLOBALS['ERROR_MSG'] = array(
      "404-Acct-Invalid"      =>  "Invalid User Credentials",
      "SYS_KEY_ERROR"         =>  "Unable to verify PIP server key, try update to new repo or check internet connection.",
      "SYS_KEY_INVALIDATED"   =>  "Your system uses invalid bad signature key. This will disable the update services. Try update your repo or contact support@kanthzone.com for assistance.",
    );
    $GLOBALS['STATUS_MSG'] = array(
      "202-Reg-Install"   => "Installation & Registration Successfull.",
      "202-Reg"           => "Registration Successfull.",
      "SYS-KEY-STATUS-OK"     => "OK",
      "SYS-KEY-STATUS-NOK"     => "NOK"

    );
    $GLOBALS['TITLES'] = array(
      "/login"   =>  "ANP Manager Login",
      "/home"    =>  "ANP Manager Dashboard"
    );

    $GLOBALS['PAGE-TITLES'] = $GLOBALS['TITLES'][strtok($_SERVER['REQUEST_URI'],"?")];
    $GLOBALS['SYS-KEY-SERVER_URL'] = "https://public.kanthzone.com/api/?request_key=true&UTAG=".md5($_POST['email'])."&UDOMAIN=".md5($_POST['cloudfdomain']);
    $GLOBALS['CRON_PIP_SERVER_URL'] = "https://public.kanthzone.com/dns_manager/?myip&json&formatted=pretty";
    $GLOBALS['API_SERVER_URL'] = $GLOBALS['CRON_PIP_SERVER_URL']."&SYS_KEY=".$_SESSION['U_SYS_KEY'];

  }

  public function check_session(){
    if (isset($_SESSION['USER_LOGGED']) && $_SESSION['USER_LOGGED'] == true) {
      if ($this::get_location() == '/login') {
        $this::redirect_route('home');
        return true;
      }
    }else{
      if ($this::get_location() == '/home') {
        $this::redirect_route('default');
        return true;
      }
    }
    return false;
  }

  public function is_user_loggedin(){
    if (isset($_SESSION['USER_LOGGED']) && $_SESSION['USER_LOGGED'] == true) {
      return true;
    }
    return false;
  }

  public function get_content_template(){

    $TEMPLATES = array(
      "/home" => "./template/home_content.php",
      "/error" => "./template/404.php"
    );

    return $TEMPLATES[$this::get_location()];

  }

  public function set_alert($TYPE = 'success', $TITLE = "Success", $INFO = " Successfully Completed." ){
    $_SESSION['ALERT_MSG']  = true;
    $_SESSION['ALERT_TYPE'] = $TYPE;
    $_SESSION['ALERT_TITLE'] = $TITLE;
    $_SESSION['ALERT_INFO'] = $INFO;
  }

  public function get_user_tag(){
    if (!isset($_SESSION['U_TAG'])) {
      $_SESSION['U_TAG'] = md5($_SESSION['U_EMAIL']);
    }
    return $_SESSION['U_TAG'];
  }

  public function get_domain_info($ID){
    $SQL = "subdomains WHERE id ='".$ID."'";
    $DOMAIN_INFO = $this->gen_get_db_data($SQL,true)[0];
    return $DOMAIN_INFO;
  }

  public function check_server_connectivity(){
    $CHECK_SERVER_RESPONSE = json_decode($this->external_com($GLOBALS['CRON_PIP_SERVER_URL']."&SYS_KEY=".$this->get_sys_key()),true);
    if ($CHECK_SERVER_RESPONSE['request-data']['Msg']['request-type'] == "NRPLY"){
      return false;
    }
    return true;
  }

  public function check_sys_key_status (){
    if ($_SESSION['U_SYS_KEY_STATUS'] == 0){
      $_SESSION['SYS_KEY_ERROR_ALERT'] = true;
      $_SESSION['SYS_KEY_STATUS'] = $GLOBALS['STATUS_MSG']["SYS-KEY-STATUS-NOK"];
      $_SESSION['SYS_KEY_ERROR'] = $GLOBALS['ERROR_MSG']['SYS_KEY_INVALIDATED'];
      return true;
    }
    $_SESSION['SYS_KEY_ERROR_ALERT'] = false;
    $_SESSION['SYS_KEY_STATUS'] = $GLOBALS['STATUS_MSG']["SYS-KEY-STATUS-OK"];
    $_SESSION['API_SERVER_REPLY'] = $CHECK_SERVER_RESPONSE;
    return false;
  }

}
