<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0.0 27/Nov/2023
* DB Communication Class File
* v1.0.1 12/9/23
*/
class DB_COMM extends GENERAL_CONFIGURATION{

  public function reader($DATA,$VAL){
    $characters[] = $DATA; // decode the feed
    foreach ($characters as $character) {
      $OUTPUT   = $character->$VAL;
    }
    return $OUTPUT;
  }

  protected function db_detail(){


      define("SERVER", "localhost");
      define('DB', 'anp_dns');
      define("DB_ID", "anp_dns");
      define('DB_PWD', 'b89493f92ecf22049940eec9217d5611');
      define('SESSION_TIME',"60"); //minutes

    //   define('DB', 'anp_dns');
    //   define("SERVER", "db");    // }
    //   define("DB_ID", "anp_dns_manager_beta");
    //   define('DB_PWD', 'ca10a1c7511757913b66ea5da0179ee3');
    //   define('SESSION_TIME',"60"); //minutes
    //



    //initialize php config
    $this->general_format();

    $OUTPUT = new stdClass;
    $OUTPUT->SERVER       = SERVER;
    $OUTPUT->DB           = DB;
    $OUTPUT->DB_ID        = DB_ID;
    $OUTPUT->DB_PWD       = DB_PWD;
    $OUTPUT->SESSION_TIME = SESSION_TIME;
    return $OUTPUT;
  }



  public function gen_get_db_data($TABLE_NICK,$REQ){
    //GENERAL database data reader

    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));
    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $sql = "SELECT * FROM ".$TABLE_NICK;
    if($REQ !== true){
      $sql = "SELECT * FROM ".$TABLE_NICK." WHERE ".$REQ;
    }
    if($TABLE_NICK == false){
      $sql = $REQ;
    }
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        $DATA[] = $row;
      }
    }
    return $DATA;
  }

  public function gen_check_db_data($TABLE_NICK,$REQ){
    //GENERAL database data reader

    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));
    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $sql = "SELECT * FROM ".$TABLE_NICK;
    if($REQ !== true){
      $sql = "SELECT * FROM ".$TABLE_NICK." WHERE ".$REQ;
    }
    if($TABLE_NICK == false){
      $sql = $REQ;
    }

    $result = $con->query($sql);

    if ($result->num_rows > 0) {

      return true;
    }else{
      return false;
    }
    return $con->error;
  }

  public function create_entry($TABLE,$COLUMN,$QUERY){
    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $query   = "INSERT INTO `".$TABLE."` (".$COLUMN.") VALUES (".$QUERY.") ";

    if ($con->query($query) === TRUE ) {
      return true;
    }

    return  $con->error;

  }

  public function update_db_data($TABLE,$COLUMN,$DATA,$IDENTIFIER,$IDENTIFIER_VAL,$REQ = true){

    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $query   = "UPDATE `".$TABLE."` SET ".$COLUMN." = '".$DATA."' WHERE ".$IDENTIFIER." = '".$IDENTIFIER_VAL."'";
    if ($REQ === false){
      $query   = "UPDATE `".$TABLE."` ".$COLUMN.$DATA." WHERE ".$IDENTIFIER." = '".$IDENTIFIER_VAL."'";

    }

    if ($COLUMN == false) {
      $query = "UPDATE `".$TABLE."` SET ".$DATA." WHERE ".$IDENTIFIER." = '".$IDENTIFIER_VAL."'";
    }
    

    if ($con->query($query) === TRUE ) {
      return true;
    }

    return  $con->error;

  }


  public function remove_entry($QUERY){
    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $query   = "DELETE FROM ".$QUERY;

    if ($con->query($query) === TRUE ) {
      return true;
    }

    return  $con->error;
  }

  protected function send_login_notification($UID){

#For future


  }


  public function check_user(){

    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $USER_EMAIL = strtolower(trim(htmlspecialchars($_POST['email'])));
    $query   = "SELECT * FROM users WHERE `email` = '".$USER_EMAIL."' AND  `password` = '".base64_encode(md5(trim($_POST['password'])))."' ";
    $result = $con->query($query);

    if ($result->num_rows > 0) {

      #get_domain_details
      $SQL_2= "domains WHERE user_tag = '".md5($USER_EMAIL)."' ";
      $DOMAINS = $this->gen_get_db_data($SQL_2,true)[0];

      // output data of each row
      while($row = $result->fetch_assoc()) {

        if (session_status() == PHP_SESSION_NONE ) {
          session_start();
        }

        #Portion to send login email alert

        #set sessions
        $_SESSION['USER_LOGGED'] = true;
        $_SESSION['U_USERNAME'] = $row['email'];
        $_SESSION['U_NAME']     = $row['name'];
        $_SESSION['U_EMAIL']    = $row['email'];
        $_SESSION['U_SYS_KEY']    = $row['sys_key'];
        $_SESSION['U_SYS_KEY_STATUS']    = $row['sys_key_status'];
        $_SESSION['U_PRIVILEGES'] = $row['privileges'];
        $_SESSION['U_CLOUDAPI'] = $DOMAINS['cloudfapi'];
        $_SESSION['U_CLOUDFD_DOMAIN'] = $DOMAINS['cloudfdomain'];
        $_SESSION['U_TAG']      = $row['user_tag'];
        $_SESSION['U_CLOUDF_EMAIL']   = $DOMAINS['cloudfemail'];
        $_SESSION['U_ID']       = $row['id'];

        #Record login timestamp 23 May 2023
        // $this->update_db_data('users','last_login',time(),'username',$row['username'],true);

        #Check api server key
        #Do Not remove this <<-
        // $this::check_server_connectivity();
        $this::check_sys_key_status();

        #redirect
        $this::redirect_route('home');
        exit;

      }
    }else{
      $this::redirect_route('login?error=404-Acct-Invalid');
      exit;

    }

    return $con->error;

  }

  public function get_table($TABLE,$ROOT){
    $DB_DATA = $this->db_detail();
    $con = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    #Check db connection
    if ($con->connect_error){
      die ('Connection failed: '. $con->connect_error);
    }

    $query  = "DESCRIBE ".$TABLE;
    $result = $con->query($query);
    $OUTPUT = new stdClass;

    if($result->num_rows > 0){

      while($row = $result->fetch_assoc()) {

        if ($ROOT == "raw"){ //all field in array
          $FIELDS[] = $row['Field'];
        }
        if ( $row['Field'] != 'id' && $ROOT == false){
          $OUTPUT->FIELDS[] = $row['Field'];
        }
        if ($ROOT == true){
          $OUTPUT->FIELDS[] = $row['Field'];
        }
      }
    }
    $DATA = $this->reader($OUTPUT,"FIELDS");
    $COLUMN = implode(",",$DATA);
    if ($ROOT == "raw"){
      return $FIELDS;
    }

    return $COLUMN;
  }


  public function initialise(){
    $DB_DATA = $this->db_detail();
    $conn = new mysqli($this->reader($DB_DATA,'SERVER'), $this->reader($DB_DATA,'DB_ID'), $this->reader($DB_DATA,'DB_PWD'), $this->reader($DB_DATA,'DB'));

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // sql to user create table
    $sql = "CREATE TABLE IF NOT EXISTS users (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(30) NOT NULL,
      password VARCHAR(200) NOT NULL,
      email VARCHAR(50),
      privileges int(1),
      user_tag VARCHAR(200),
      sys_key VARCHAR(255),
      sys_key_status int(1),
      reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";


    $sql2 = "CREATE TABLE IF NOT EXISTS subdomains (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(30) NOT NULL,
      subdomain VARCHAR(30) NOT NULL,
      description VARCHAR(200) NOT NULL,
      proxy INT(1),
      user_tag VARCHAR(200),
      status INT(1)
    )";

    $sql3 = "CREATE TABLE IF NOT EXISTS ip_history (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      current VARCHAR(15) NOT NULL,
      previous VARCHAR(15) NOT NULL,
      change_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $sql4 = "CREATE TABLE IF NOT EXISTS update_tracker (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      domain VARCHAR(50) NOT NULL,
      user_tag VARCHAR(200),
      response MEDIUMTEXT NOT NULL,
      response_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $sql5 = "CREATE TABLE IF NOT EXISTS domains (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_tag VARCHAR(200),
      cloudfapi VARCHAR(200),
      cloudfdomain VARCHAR(50),
      cloudfemail VARCHAR(50),
      status INT(1),
      response_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $sql6 = "CREATE TABLE IF NOT EXISTS new_entry_pool (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      domain VARCHAR(100),
      subdomain VARCHAR(100),
      user_tag VARCHAR(200),
      status int(1),
      entry_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";


    if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE && $conn->query($sql3) === TRUE && $conn->query($sql4) === TRUE && $conn->query($sql5) === TRUE && $conn->query($sql6) === TRUE) {

      return true;
    } else {
      if (DEBUG_MODE == true) {
        return "Error creating table: " . $conn->error;
      }
      return false;
    }

    $conn->close();

  }


}
