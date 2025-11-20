
// admin/logout.php - Create separate file

<?php
session_start();
session_destroy();
header('Location: login.php');
exit();
?>
