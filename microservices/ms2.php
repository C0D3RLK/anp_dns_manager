<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0.1 Ms Dec 1,2023
* 1.0.2 MS2 Jan 24, 2024 - PIP server load fix
*/

define("MODE","PROD");

if (class_exists('GENERAL_CONFIGURATION')!=true) {
  if (MODE != "LAB") {
    require_once "/var/www/html/config/general.php";
  }else{
    require_once "../html/config/general.php";
  }
}

if (class_exists('DB_COMM')!=true) {
  if (MODE != "LAB") {
    require_once "/var/www/html/config/db.php";
  }else{
    require_once "../html/config/db.php";
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
      #updated JAN 3rd,2024
      $this->update_tracker($user_tag,$user_subdomain,"Up-Todate, No PIP Change");
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

  public function get_users($CURRENT_IP){
    #get_user
    $USERS = $this->gen_get_db_data("users",true)[0];
    $USER_DOMAIN = $this->gen_get_db_data("domains WHERE user_tag='".$USERS['user_tag']."' AND status = '1'",true)[0];
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

  private function expire_pool_entry($ID){
    $RUN_CMD = $this->update_db_data('new_entry_pool','status','1','id',$ID);
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

  public function check_pool(){
    $ENTRIES   = $this->gen_get_db_data("new_entry_pool","status = '0'");

    if ($ENTRIES != NULL) {
      #Fix to prevent server load only checks IP when there's request entry
      if ($DNS_MAN->check_server_connectivity() == false) {
        $SERVER_STAT = $DNS_MAN->update_db_data('users','sys_key_status','0','privileges',"0",$REQ = true);
        exit;
      }
      $NEW_PIP   = $this::get_pip("IP","p_ip",$this->get_sys_key());
      for ($i=0; $i < COUNT($ENTRIES); $i++) {
        $USER_DATA    = $this->gen_get_db_data("domains","cloudfdomain = '".$ENTRIES[$i]['domain']."' AND user_tag = '".$ENTRIES[$i]['user_tag']."' AND status = '1'")[0];
        $DOMAIN_SETTING = $this->gen_get_db_data("subdomains","user_tag='".$ENTRIES[$i]['user_tag']."' AND subdomain='".$ENTRIES[$i]['subdomain']."' AND status = '1'")[0]['proxy'];
        $FULL_DOMAIN = $ENTRIES[$i]['subdomain'].".".$ENTRIES[$i]['domain'];
        #Perform force domain update
        $this->expire_pool_entry($ENTRIES[$i]['id']);
        $this->update_domain($ENTRIES[$i]['domain'],$USER_DATA['cloudfapi'],$USER_DATA['cloudfemail'],$FULL_DOMAIN,$DOMAIN_SETTING,$NEW_PIP,$ENTRIES[$i]['user_tag'],$ENTRIES[$i]['subdomain']);

      }
    }
    return false;
  }

}

$DNS_MAN = new ANP_DNS_MICROSRV();
$DNS_MAN->GENERAL_VARIABLES();
//if ($DNS_MAN->check_server_connectivity() == false) {
  //$SERVER_STAT = $DNS_MAN->update_db_data('users','sys_key_status','0','privileges',"0",$REQ = true);
  //exit;
//}
#Pool 1
// $DNS_MAN->check_ip_changes();

#Pool 2
#Check pool for new entry
#run update ip and set status to 1
$DNS_MAN->check_pool();
