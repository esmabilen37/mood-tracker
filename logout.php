<?php
//kullanıcı çıkış yapabilsin diye
session_start();
session_destroy();
header("Location: login.php");
exit;
