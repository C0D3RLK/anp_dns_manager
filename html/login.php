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

$DNS_MAN = new DB_COMM();
$DNS_MAN::GENERAL_VARIABLES();

$DNS_MAN->check_session();

if (isset($_POST['email'])) {

  $FILTER_PASS = base64_encode(md5($_POST['password']));
  $QUERY = "SELECT * FROM users WHERE email='$_POST[email]' AND password = '{$FILTER_PASS}'";

  // $USERDATA = $DNS_MAN->gen_check_db_data(false,$QUERY);
  // if($USERDATA == false){
  //   $DNS_MAN->redirect_route('login?error=404-Acct-Invalid');
  //   exit;
  // }
  // if ($USERDATA == true) {
  //
  //   $DNS_MAN->redirect_route('home');
  //   exit;
  // }

  $DNS_MAN->check_user();

}

?>

<?php require_once './app/header.php'; ?>

<body class="main-bg">

  <div class="sidenav">
    <div class="login-main-text">
      <h1>ANP-DNS<br> Manager</h1>
      <p>Login from here to manage all your subdomains.

      </br>
      </br>
       <small class="h1"><i class="fa-brands fa-cloudflare text-warning"></i> </small><br><small>Cloudflare API v4</small>
       <br><br>
         <br><br>
         <small>v1.0.0</small>
    </div>
  </div>
  <div class="main">
    <div class="col-md-6 col-sm-12">
      <div class="login-form">
        <form id="login_form" action="./login" method="post">
          <div class="form-group h2">
            <label>User Name</label>
            <input type="text" name="email" class="form-control" placeholder="User Name" required>
          </div>
          <div class="form-group h2">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <button type="submit" form="login_form" class="btn btn-black " data-size="md">Login</button>
          <!-- <button type="submit" class="btn btn-secondary">Register</button> -->
        </form>
        <br>
        <label class="text-danger h5"  for="error_msg"><?php if (isset($_GET['error'])){ echo $GLOBALS['ERROR_MSG'][$_GET['error']]; } ?></label>
        <label class="text-success h5"  for="success_msg"><?php if (isset($_GET['status'])){ echo $GLOBALS['STATUS_MSG'][$_GET['status']]; } ?></label>
      </div>
    </div>
  </div>

</body>
</html>
