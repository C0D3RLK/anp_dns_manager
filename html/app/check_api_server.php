<?php
// if (session_status() == PHP_SESSION_NONE ) {
//   session_start();
// }
// if (class_exists('GENERAL_CONFIGURATION')!=true) {
//   require_once '../config/general.php';
// }
//
// if (class_exists('DB_COMM')!=true) {
//   require_once '../config/db.php';
// }
// $DNS_MAN2 = new DB_COMM();
// $DNS_MAN->check_server_connectivity();

if (isset($_SESSION['SYS_KEY_STATUS']) && $_SESSION['SYS_KEY_STATUS'] == "NOK"): ?>
 <h5 class="text-warning text-center"> <?php echo $GLOBALS['ERROR_MSG']['SYS_KEY_INVALIDATED']; ?> </h5>
<?php endif; ?>
