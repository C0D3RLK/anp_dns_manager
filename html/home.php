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
require_once './app/header.php';


?>
<body class="main-bg" type="home-dark">
  <!-- </div> -->


<?php
  if (isset($_SESSION['SYS_KEY_ERROR_ALERT']) && $_SESSION['SYS_KEY_ERROR_ALERT'] == true): ?>
  <h5 class="text-warning text-center"> <?php echo $GLOBALS['ERROR_MSG']['SYS_KEY_INVALIDATED']; ?> </h5>
<?php endif; ?>

<!-- <div class="sidenav col-md-4 col-lg-2">
<div class="login-main-text">
<h1>ANP-DNS<br> Manager</h1>
<p>Manage all your subdomains.

</br>
</br>
<small class="h1"><i class="fa-brands fa-cloudflare text-warning"></i> </small><br><small>Cloudflare API v4</small>
<br><br>
<br><br>
<small>v1.0.0</small>
</div>
</div> -->

<div class="main" data-type="home">
  <!-- <div class="col-md-6 col-lg-12"> -->
  <div class="container home" data-type='content-box' >
    <?php require_once $DNS_MAN->get_content_template(); ?>
  </div>
  <!-- </div> -->
</div>
</body>
