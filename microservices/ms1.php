<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0.0 Ms Dec 1,2023
*/

define("MODE","PROD");

if (class_exists('GENERAL_CONFIGURATION')!=true) {
  if (MODE != "LAB") {
    require_once "/var/www/html/config/general.php";
  }else{
    require_once "./config/general.php";
  }
}

if (class_exists('DB_COMM')!=true) {
  if (MODE != "LAB") {
    require_once "/var/www/html/config/db.php";
  }else{
    require_once "./config/db.php";
  }
}

class ANP_DNS_MICROSRV extends DB_COMM{

  public function get_pip($DATA_ARRAY ="IP",$REQ_DATA = 'p_ip',$SYS_KEY = "null"){

    $url = $GLOBALS['CRON_PIP_SERVER_URL']."&SYS_KEY=".$SYS_KEY;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    #This headers contains public keys do not change anything
    $headers = array(
      "Accept: application/json",
      "User-Agent: Kanthzone PublicAPI Client V1.0",
      "Authorization: Bearer 3cb2739b3a6061358277ddadad12060d"
    );
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLINFO_HEADER_OUT, $headers);

    //for debug only!
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    $OUTPUT = json_decode($response,true)['request-data'][$DATA_ARRAY][$REQ_DATA];
    curl_close($curl);

    return $OUTPUT;
    // header('Content-Type: application/json; charset=utf-8');

    #you can also request other details from your IP
    #DATA_ARRAY = Region
    #REQ_DATA = country
    #REQ_DATA = CountryCode
    #REQ_DATA = geo_latitude
    #REQ_DATA = geo_longitude
    #REQ_DATA = geo_continent
    #default set to public ip
  }

  public function update_domain($cloudfdomain,$cloudfapi,$cloudfemail,$subdomain,$proxy_stat,$ip,$user_tag,$user_subdomain) {

    $proxy_stat =( $proxy_stat == 1)? true:false;

    $headers = [
      'X-Auth-Email: '.$cloudfemail,
      'X-Auth-Key: '.$cloudfapi,
      'Content-Type: application/json'
    ];
    $ch = curl_init();
    $domain = $cloudfdomain;

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $data = [
      'type' => 'A',
      'name' => $subdomain,
      'content' => $ip,
      'ttl' => 120,
      'proxied' => $proxy_stat
    ];


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones?name=$domain");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      exit('Error: ' . curl_error($ch));
    }
    curl_close ($ch);

    $json = json_decode($result, true);

    $ZoneID = $json['result']['0']['id'];


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$ZoneID/dns_records?name=$subdomain");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      exit('Error: ' . curl_error($ch));
    }
    curl_close ($ch);

    $json = json_decode($result, true);

    $DNSID = $json['result']['0']['id'];

    $old_ip = $json['result']['0']['content'];

    if ($old_ip === $ip) {
      #Do nothing
    }
    else {

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$ZoneID/dns_records/$DNSID");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $result = curl_exec($ch);

      if (curl_errno($ch)) {
        exit('Error: ' . curl_error($ch));
      }

      $this->update_tracker($user_tag,$user_subdomain,$result);

      return $result;

    }


  }

  public function update_tracker($user_tag,$domain,$response){ #log_writer
    #Write the response to DB on subdomain
    $this->create_entry('update_tracker',"domain,user_tag,response","'".$domain."','".$user_tag."','".$response."'");

  }

  public function update_ip($new_ip,$previous_ip){
    #update of ip changes
    $this->create_entry('ip_history',"current,previous","'".$new_ip."','".$previous_ip."'");
  }

  public function get_previous_ip(){
    $QUERY= "ip_history ORDER by id DESC LIMIT 1";
    return $this->gen_get_db_data($QUERY,true)[0]['current'];
  }

  public function get_sys_key(){
    return $this->gen_get_db_data("users WHERE privileges = '0' ORDER by id ASC LIMIT 1",true)[0]['sys_key'];
  }

  function get_users($CURRENT_IP){
    #get_user
    $USERS = $this->gen_get_db_data("users",true)[0];
    $USER_DOMAIN = $this->gen_get_db_data("domains WHERE user_tag='".$USERS['user_tag']."'",true)[0];
    $USER_DOMAIN_QUERY = "subdomains WHERE status ='1' && user_tag='".$USERS['user_tag']."'";
    $USER_SUBDOMAINS = $this->gen_get_db_data($USER_DOMAIN_QUERY,true);
    $SUBDOMAIN_DATA['cloudfapi'] = $USER_DOMAIN['cloudfapi'];
    $SUBDOMAIN_DATA['cloudfdomain'] = $USER_DOMAIN['cloudfdomain'];
    $SUBDOMAIN_DATA['cloudfemail'] = $USER_DOMAIN['cloudfemail'];
    $SUBDOMAIN_DATA['cloudfemail'] = $USER_DOMAIN['cloudfemail'];

    if (COUNT($USER_SUBDOMAINS) != 0 ) {
      for ($i=0; $i < COUNT($USER_SUBDOMAINS) ; $i++) {
        $FULL_DOMAIN = $USER_SUBDOMAINS[$i]['subdomain'].".".$SUBDOMAIN_DATA['cloudfdomain'];
        $this->update_domain($SUBDOMAIN_DATA['cloudfdomain'],$SUBDOMAIN_DATA['cloudfapi'],$SUBDOMAIN_DATA['cloudfemail'],$FULL_DOMAIN,$USER_SUBDOMAINS[$i]['proxy'],$CURRENT_IP,$USERS['user_tag'],$USER_SUBDOMAINS[$i]['subdomain']);
      }

    }

  }
  
  public function check_ip_changes(){
    $GET_PREVIOUS_IP = $this->get_previous_ip();
    $PREVIOUS_IP  =($GET_PREVIOUS_IP == NULL)? "0.0.0.0":$GET_PREVIOUS_IP ;
    $CURRENT_IP   = $this::get_pip("IP","p_ip",$this->get_sys_key());

    if ($PREVIOUS_IP != $CURRENT_IP ) {
      $this->update_ip($CURRENT_IP,$PREVIOUS_IP);
      $this->get_users($CURRENT_IP);
      return true;
    }
    return false;
  }

}

$DNS_MAN = new ANP_DNS_MICROSRV();
$DNS_MAN->GENERAL_VARIABLES();
if ($DNS_MAN->check_server_connectivity() == false) {
  $SERVER_STAT = $DNS_MAN->update_db_data('users','sys_key_status','0','privileges',"0",$REQ = true);
  exit;
}
$DNS_MAN->check_ip_changes();
